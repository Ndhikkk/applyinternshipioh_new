<?php

namespace App\Services;

class CvAnalysisService
{
    private $apiKeys = [];

    public function __construct()
    {
        // Load API keys from environment variables (set in .env file)
        // This is more secure than hardcoding keys in the source code
        $this->apiKeys = array_filter([
            getenv('DEEPSEEK_API_KEY'),
            getenv('GROQ_API_KEY'),
            getenv('OPENAI_API_KEY'),
            getenv('GEMINI_API_KEY_1'),
            getenv('GEMINI_API_KEY_2'),
            getenv('GEMINI_API_KEY_3'),
            getenv('GEMINI_API_KEY_4'),
            getenv('GEMINI_API_KEY_5'),
        ]);

        // Fallback to default keys if environment variables are not set (development only)
        if (empty($this->apiKeys) && ENVIRONMENT !== 'production') {
            $this->apiKeys = [
                // Keys should be placed in .env file
            ];
        }
    }

    public function analyze($filePath)
    {
        set_time_limit(120);

        if (!file_exists($filePath)) {
            return ['error' => 'File not found'];
        }

        // 1. Extract Text Locally (Using pdftotext)
        $fullText = $this->extractPdfText($filePath);
        if (empty($fullText) || strlen($fullText) < 50) {
            return $this->getMockAnalysis(basename($filePath));
        }

        // Calculate Personality locally (Always do this as it's purely rule-based and fast)
        $personality = $this->analyzePersonality($fullText);

        // 2. Try AI Analysis
        $llmResult = $this->callLLM($fullText);

        if (!$llmResult || isset($llmResult['error'])) {
             // 3. Fallback to Rule-Based Engine
             error_log("All API keys failed. Switching into Rule-Based Engine.");
             $localResult = $this->analyzeLocalRules($fullText);
             // Merge Personality
             $localResult['data']['personality'] = $personality;
             return $localResult;
        }

        // AI Success
        $category = $llmResult['category'] ?? '';
        $division = $this->mapDivision($category); 
        
        return [
            'success' => true,
            'data' => [
                'division' => $division,
                'category' => $category,
                'skills' => $this->extractSkills($fullText),
                'education' => $this->extractEducation($fullText),
                'experience_years' => $this->estimateExperience($fullText),
                'personality' => $personality, // Add personality to AI result too
                'is_mock' => false
            ]
        ];
    }

    private function extractPdfText($filePath)
    {
        $mimeType = mime_content_type($filePath);
        if (strpos($mimeType, 'pdf') !== false) {
            // 1. Try Smalot PDF Parser (Best for Hosting)
            if (class_exists('Smalot\PdfParser\Parser')) {
                try {
                    $parser = new \Smalot\PdfParser\Parser();
                    $pdf = $parser->parseFile($filePath);
                    $text = $pdf->getText();
                    if (!empty($text)) return substr($text, 0, 12000);
                } catch (\Throwable $e) {
                    error_log("Smalot Parser Failed: " . $e->getMessage());
                }
            }

            // 2. Fallback to pdftotext (Linux Server with Poppler)
            $cmd = "pdftotext -layout " . escapeshellarg($filePath) . " -";
            $text = shell_exec($cmd);
            if ($text) return substr($text, 0, 12000); 
        }
        return '';
    }

    private function callLLM($text)
    {
        foreach ($this->apiKeys as $key) {
             $isOpenAI = strpos($key, 'sk-proj-') === 0;
             $isDeepSeek = !$isOpenAI && strpos($key, 'sk-') === 0;
             $isGroq = strpos($key, 'gsk_') === 0;

             $prompt = "You are a CV Parser. Return ONLY JSON. Keys: 'full_text' (summary), 'category' (job title). Parse this CV:\n\n" . $text;

             if ($isOpenAI) {
                 $url = "https://api.openai.com/v1/chat/completions";
                 $data = [
                    'model' => 'gpt-4o-mini',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'response_format' => ['type' => 'json_object']
                 ];
                 $headers = ['Authorization: Bearer ' . $key, 'Content-Type: application/json'];
             } elseif ($isDeepSeek) {
                 $url = "https://api.deepseek.com/chat/completions";
                 $data = [
                    'model' => 'deepseek-chat',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'response_format' => ['type' => 'json_object']
                 ];
                 $headers = ['Authorization: Bearer ' . $key, 'Content-Type: application/json'];
             } elseif ($isGroq) {
                 $url = "https://api.groq.com/openai/v1/chat/completions";
                 $data = [
                    'model' => 'llama3-8b-8192',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'response_format' => ['type' => 'json_object']
                 ];
                 $headers = ['Authorization: Bearer ' . $key, 'Content-Type: application/json'];
             } else {
                 $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$key}";
                 $geminiPrompt = "Return JSON with keys 'full_text' and 'category'. Analyze this: " . $text;
                 $data = [
                    'contents' => [['parts' => [['text' => $geminiPrompt]]]],
                    'generationConfig' => ['response_mime_type' => 'application/json']
                 ];
                 $headers = ['Content-Type: application/json'];
             }

             $ch = curl_init($url);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_POST, true);
             curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
             curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
             curl_setopt($ch, CURLOPT_TIMEOUT, 30);
             
             $response = curl_exec($ch);
             $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
             curl_close($ch);

             if ($httpCode === 200) {
                  $json = json_decode($response, true);
                  if ($isGroq || $isDeepSeek || $isOpenAI) {
                      $content = $json['choices'][0]['message']['content'] ?? '{}';
                  } else {
                      $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                  }
                  $cleanJson = str_replace(['```json', '```'], '', $content);
                  return json_decode($cleanJson, true);
             }
             continue;
        }
        return ['error' => 'All keys failed'];
    }

    private function analyzeLocalRules($text)
    {
        $text = strtolower($text);
        
        $divisions = [
            'Markom' => [
                'keywords' => ['marketing', 'communication', 'branding', 'public relations', 'media', 'campaign', 'brand awareness', 'citra perusahaan', 'media relations', 'strategy', 'market research', 'social media', 'content creator', 'copywriting', 'advertising', 'ads', 'seo', 'sem', 'digital marketing', 'event management'],
                'study_programs' => ['ilmu komunikasi', 'marketing', 'manajemen', 'public relations', 'desain komunikasi visual', 'dkv', 'jurnalistik', 'broadcasting', 'periklanan'],
                'category' => 'Marketing Communication',
                'description' => 'Strategi komunikasi pemasaran & branding'
            ],
            'Elang IT' => [
                'keywords' => ['information technology', 'software', 'infrastructure', 'cybersecurity', 'system', 'operasional', 'development', 'programming', 'network', 'support', 'teknologi', 'web', 'mobile', 'android', 'ios', 'backend', 'frontend', 'fullstack', 'devops', 'cloud', 'server', 'database', 'sql', 'api'],
                'study_programs' => ['informatika', 'teknik informatika', 'sistem informasi', 'teknologi informasi', 'rekayasa perangkat lunak', 'rpl', 'ilmu komputer', 'computer science', 'teknik komputer'],
                'category' => 'Information Technology',
                'description' => 'Pengembangan sistem & infrastruktur IT'
            ],
            'Technical' => [
                'keywords' => ['network', 'maintenance', 'telekomunikasi', 'troubleshooting', 'optimization', 'fiber optic', 'teknis', 'performa jaringan', 'telecom', 'radio', 'transmisi', 'bts', '4g', '5g', 'ip', 'tcp', 'routing', 'switching', 'hardware', 'iot', 'elektronika'],
                'study_programs' => ['teknik telekomunikasi', 'teknik elektro', 'teknik jaringan', 'teknik komputer', 'teknik listrik'],
                'category' => 'Technical Network',
                'description' => 'Maintenance jaringan telekomunikasi'
            ],
            'Finance' => [
                'keywords' => ['finance', 'regional', 'analysis', 'budgeting', 'reporting', 'keuangan', 'accounting', 'asset', 'laporan keuangan', 'anggaran', 'tax', 'pajak', 'audit', 'treasury', 'banking', 'reconciliation', 'ledger', 'neraca', 'laba rugi'],
                'study_programs' => ['akuntansi', 'manajemen keuangan', 'ekonomi', 'administrasi bisnis', 'perpajakan', 'komputerisasi akuntansi'],
                'category' => 'Finance Circle Java',
                'description' => 'Analisis finansial & budgeting'
            ],
            'B2B' => [
                'keywords' => ['business to business', 'b2b', 'corporate', 'enterprise', 'sales', 'client', 'solusi enterprise', 'relasi klien', 'penjualan korporat', 'account manager', 'negotiation', 'canvas', 'prospecting', 'crm', 'business development', 'partnerships'],
                'study_programs' => ['manajemen', 'administrasi bisnis', 'bisnis digital', 'marketing', 'teknik industri', 'administrasi niaga'],
                'category' => 'Business to Business',
                'description' => 'Pengembangan bisnis korporat'
            ],
            'Sosmed 3 & IM3' => [
                'keywords' => ['social media', 'content', 'community', 'engagement', 'analytics', 'instagram', 'tiktok', 'konten', 'komunitas', 'kol', 'influencer', 'viral', 'trend', 'storytelling', 'reels', 'youtube', 'facebook', 'twitter', 'social strategy'],
                'study_programs' => ['ilmu komunikasi', 'digital marketing', 'manajemen', 'dkv', 'bisnis digital', 'sastra', 'multimedia'],
                'category' => 'Social Media Management',
                'description' => 'Manajemen konten & komunitas'
            ],
            'Daily Project' => [
                'keywords' => ['project management', 'coordination', 'monitoring', 'reporting', 'operasional', 'team', 'progres', 'admin', 'scrum', 'agile', 'timeline', 'schedule', 'planning', 'logistics', 'inventory', 'warehouse', 'supply chain'],
                'study_programs' => ['manajemen', 'teknik industri', 'sistem informasi', 'administrasi bisnis', 'manajemen proyek', 'logistik'],
                'category' => 'Daily Project Support',
                'description' => 'Manajemen operasional harian'
            ],
            'Project Post Paid' => [
                'keywords' => ['postpaid', 'post paid', 'retention', 'customer', 'value added', 'service', 'strategy', 'pascabayar', 'pelanggan', 'churn', 'loyalty', 'billing', 'subscription', 'paket', 'bundling', 'segmentasi'],
                'study_programs' => ['manajemen', 'bisnis digital', 'marketing', 'sistem informasi', 'teknik industri', 'statistika'],
                'category' => 'Postpaid Product',
                'description' => 'Manajemen produk pasca bayar'
            ]
        ];

        $bestScore = 0;
        $bestDivision = 'Capability Building'; // Default
        $bestCategory = 'Human Resources / General';

        foreach ($divisions as $name => $data) {
            $score = 0;
            
            // 1. Keyword Score (Use strpos for existence, substr_count overkill for simple matches)
            foreach ($data['keywords'] as $kw) {
                if (strpos($text, strtolower($kw)) !== false) {
                    $score += 3; // Basic match points
                }
            }
            
            // 2. Educational Background Score (Huge Booster - The "Jurusan" Factor)
            if (isset($data['study_programs'])) {
                foreach ($data['study_programs'] as $prog) {
                    if (strpos($text, strtolower($prog)) !== false) {
                        $score += 20; // Critical Booster: If Diploma matches, they almost certainly belong here
                    }
                }
            }

            // 3. Selection Logic (Allow override if score is higher OR if score is same but current best is default)
            // Fix: If tie, usually keep first, but ensure we beat 0.
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestDivision = $name;
                $bestCategory = $data['category'];
            }
        }

        // 3. AUTO-ALIGNMENT BOOST (The "Smart Recruiter" Logic)
        $p = $this->analyzePersonality($text);
        
        switch ($bestDivision) {
            case 'Markom':
            case 'Sosmed 3 & IM3':
                $p['Creative'] += 15;
                $p['Communicative'] += 15;
                break;
            case 'Elang IT':
                $p['Technical_Depth'] += 15;
                $p['Analytical'] += 10;
                break;
            case 'Finance':
                $p['Structured'] += 15;
                $p['Analytical'] += 15;
                break;
            case 'Technical':
                $p['Technical_Depth'] += 15;
                $p['Execution'] += 10;
                break;
            case 'B2B':
                $p['Communicative'] += 15;
                $p['Execution'] += 10;
                break; 
            case 'Daily Project':
            case 'Project Post Paid':
                $p['Leadership'] += 10;
                $p['Structured'] += 15;
                break;
        }

        // Clamp again after boost
        foreach ($p as $k => $v) {
            if ($v > 98) $p[$k] = 98; // Cap at 98
        }

        return [
            'success' => true,
            'data' => [
                'division' => $bestDivision,
                'category' => $bestCategory . ' (Ultra AI-Rule)',
                'skills' => $this->extractSkills($text),
                'education' => $this->extractEducation($text),
                'experience_years' => $this->estimateExperience($text),
                'personality' => $p,
                'is_mock' => true,
                'is_rule_based' => true
            ]
        ];
    }
    
    // NEW: Rule-Based Personality Analysis (Enhanced)
    private function analyzePersonality($text)
    {
        $text = strtolower($text);
        
        $dims = [
            'Analytical' => 0,
            'Creative' => 0,
            'Structured' => 0,
            'Communicative' => 0,
            'Leadership' => 0,
            'Technical_Depth' => 0,
            'Execution' => 0,
            'Adaptability' => 0
        ];

        // 1. ANALYTICAL
        if ($this->hasAny($text, ['analysis', 'data', 'research', 'evaluation', 'metrics', 'testing', 'analisis', 'riset', 'evaluasi', 'metrik', 'laporan', 'report', 'audit', 'keuangan', 'finance', 'accounting'])) {
            $dims['Analytical'] += 40;
        }
        if ($this->hasAny($text, ['excel', 'sql', 'python', 'spss', 'tableau', 'statistics', 'matlab', 'r studio', 'minitab'])) {
            $dims['Analytical'] += 25;
        }
        if ($this->hasAny($text, ['skripsi', 'thesis', 'jurnal', 'paper', 'ilmiah', 'scientific', 'studi kasus', 'case study'])) {
             $dims['Analytical'] += 15;
        }

        // 2. CREATIVE
        if ($this->hasAny($text, ['design', 'content', 'campaign', 'branding', 'creative', 'desain', 'konten', 'kreatif', 'art', 'seni', 'visual', 'ui/ux', 'multimedia'])) {
            $dims['Creative'] += 40;
        }
        if ($this->hasAny($text, ['figma', 'canva', 'adobe', 'capcut', 'photoshop', 'illustrator', 'premiere', 'after effects', 'corel'])) {
            $dims['Creative'] += 25;
        }
        if ($this->hasAny($text, ['poster', 'video', 'concept', 'konsep', 'portofolio', 'social media', 'instagram', 'tiktok', 'youtube'])) {
            $dims['Creative'] += 15;
        }

        // 3. STRUCTURED
        if ($this->hasAny($text, ['documentation', 'sop', 'reporting', 'scheduling', 'administration', 'dokumentasi', 'laporan', 'jadwal', 'administrasi', 'sekretaris', 'secretary', 'bendahara', 'treasurer'])) {
            $dims['Structured'] += 40;
        }
        if ($this->hasAny($text, ['detail', 'teliti', 'organized', 'terorganisir', 'file', 'archive', 'data entry', 'input data'])) {
            $dims['Structured'] += 20;
        }
        if ($this->hasAny($text, ['staff a', 'biro', 'divisi', 'department'])) {
             $dims['Structured'] += 15;
        }

        // 4. COMMUNICATIVE
        if ($this->hasAny($text, ['communication', 'coordination', 'presentation', 'liaison', 'komunikasi', 'koordinasi', 'presentasi', 'hubungan fa', 'public relations', 'humas'])) {
            $dims['Communicative'] += 40;
        }
        if ($this->hasAny($text, ['marketing', 'sales', 'nego', 'customer', 'layanan', 'client', 'klien', 'mc', 'moderator', 'host', 'pembicara', 'speaker'])) {
             $dims['Communicative'] += 25;
        }
        if ($this->hasAny($text, ['team', 'tim', 'social', 'sosial', 'volunteer', 'sukarelawan'])) {
             $dims['Communicative'] += 15;
        }

        // 5. LEADERSHIP
        if ($this->hasAny($text, ['lead', 'head', 'coordinator', 'pic', 'chairman', 'manager', 'ketua', 'koordinator', 'pemimpin', 'kepala', 'president', 'direktur', 'founder', 'cofounder'])) {
            $dims['Leadership'] += 45;
        }
        if ($this->hasAny($text, ['organisasi', 'organization', 'himpunan', 'bem', 'dpm', 'ukm', 'senat', 'committee', 'panitia'])) {
            $dims['Leadership'] += 20;
        }
        if ($this->hasAny($text, ['project manager', 'supervisor', 'mentor', 'asisten', 'assistant'])) {
            $dims['Leadership'] += 15;
        }

        // 6. TECHNICAL DEPTH
        if ($this->hasAny($text, ['teknik', 'engineering', 'science', 'sains', 'informatika', 'komputer', 'computer', 'sistem informasi'])) {
             $dims['Technical_Depth'] += 30;
        }
        if ($this->hasAny($text, ['programming', 'coding', 'network', 'jaringan', 'algorithm', 'algoritma', 'linux', 'cloud', 'server', 'database', 'security'])) {
            $dims['Technical_Depth'] += 30;
        }
        if ($this->hasAny($text, ['html', 'css', 'javascript', 'php', 'laravel', 'react', 'node', 'flutter', 'android', 'ios', 'c++', 'java'])) {
            $dims['Technical_Depth'] += 20;
        }

        // 7. EXECUTION
        if ($this->hasAny($text, ['implemented', 'deployed', 'executed', 'delivered', 'completed', 'finished', 'implementasi', 'eksekusi', 'penyelesaian', 'mencapai', 'achieved'])) {
            $dims['Execution'] += 40;
        }
        if ($this->hasAny($text, ['target', 'goal', 'deadline', 'tepat waktu', 'on time', 'sukses', 'success', 'berhasil', 'juara', 'winner'])) {
            $dims['Execution'] += 25;
        }
        if ($this->hasAny($text, ['proyek', 'project', 'program kerja', 'proker', 'kkn', 'magang', 'internship', 'pkl'])) {
            $dims['Execution'] += 15;
        }

        // 8. ADAPTABILITY
        if ($this->hasAny($text, ['adaptable', 'flexible', 'fast learner', 'adaptasi', 'fleksibel', 'belajar cepat', 'mudah bergaul', 'easy going'])) {
            $dims['Adaptability'] += 40;
        }
        if ($this->hasAny($text, ['training', 'pelatihan', 'course', 'kursus', 'sertifikasi', 'certification', 'bootcamp', 'workshop', 'seminar'])) {
             $dims['Adaptability'] += 30;
        }
        if ($this->hasAny($text, ['multistaking', 'dynamic', 'dinamis', 'pressure', 'tekanan', 'shift', 'remote', 'wfh'])) {
             $dims['Adaptability'] += 15;
        }

        foreach ($dims as $key => $val) {
            if ($val < 40) $val += rand(40, 55); 
            $val += rand(0, 10);
            if ($key == 'Leadership' && $val < 50) $val = rand(45, 60);
            if ($val > 95) $val = 95; 
            if ($val < 0) $val = 0;
            $dims[$key] = $val;
        }

        return $dims;
    }

    private function hasAny($text, $keywords) {
        foreach ($keywords as $kw) {
            if (strpos($text, $kw) !== false) return true;
        }
        return false;
    }

    private function getMockAnalysis($fileName)
    {
        return [
            'success' => true,
            'data' => [
                'division' => 'Capability Building',
                'category' => 'General (File Error)',
                'skills' => ['Microsoft Office', 'Communication'],
                'education' => 'S1 (Unknown)',
                'experience_years' => 0,
                'personality' => [
                    'Analytical' => 60, 'Creative' => 60, 'Structured' => 60, 'Communicative' => 60,
                    'Leadership' => 60, 'Technical_Depth' => 60, 'Execution' => 60, 'Adaptability' => 60
                ],
                'is_mock' => true
            ]
        ];
    }

    private function mapDivision($category)
    {
        $text = strtolower($category);
        if (strpos($text, 'market') !== false) return 'Markom';
        if (strpos($text, 'tech') !== false || strpos($text, 'soft') !== false || strpos($text, 'web') !== false) return 'Elang IT';
        if (strpos($text, 'network') !== false || strpos($text, 'teleco') !== false) return 'Technical';
        if (strpos($text, 'finance') !== false || strpos($text, 'account') !== false) return 'Finance';
        if (strpos($text, 'b2b') !== false || strpos($text, 'corporate') !== false) return 'B2B';
        if (strpos($text, 'social') !== false || strpos($text, 'media') !== false) return 'Sosmed 3 & IM3';
        if (strpos($text, 'project') !== false) return 'Daily Project';
        if (strpos($text, 'post') !== false || strpos($text, 'paid') !== false) return 'Project Post Paid';
        return 'Capability Building';
    }

    private function extractSkills($text)
    {
        $lowerText = strtolower($text) . ' '; // Add space for boundary checking
        
        // 1. Detect "Tech Context" to avoid false positives (e.g. "Script" in "Manuscript" -> TypeScript)
        $isTechContext = false;
        $techTriggers = ['developer', 'engineer', 'programmer', 'coding', 'stack', 'software', 'teknik', 'informatika', 'sistem informasi', 'ilmu komputer'];
        foreach ($techTriggers as $trigger) {
            if (strpos($lowerText, $trigger) !== false) {
                $isTechContext = true;
                break;
            }
        }

        $skillMap = [
            // --- STRICT IT SKILLS (Only if Tech Context OR very specific keyword) ---
            "Python" => ["python", "py charm"],
            "Java" => ["java ", "jvm", "spring boot", "springboot"], // Space to avoid "Javanese"
            "C++" => ["c++", "cpp"],
            "C#" => ["c#", ".net", "dotnet"],
            "PHP" => ["php", "laravel", "codeigniter", "symfony"],
            "JavaScript" => ["javascript", "js ", "es6", "nodejs", "node.js"],
            "TypeScript" => ["typescript"], // Strict
            "Go" => ["golang"], // Avoid "go" generic
            "Ruby" => ["ruby", "rails"],
            "Swift" => ["swift", "ios development"],
            "Kotlin" => ["kotlin"],
            "Rust" => ["rust lang"], // Avoid "rust" generic
            "Dart" => ["dart", "flutter"],
            "R" => ["r programming", "r studio"], // Avoid letter R
            
            // WEB & FRAMEWORKS
            "React" => ["react", "reactjs", "react.js", "next.js"],
            "Vue" => ["vue", "vuejs", "vue.js", "nuxt"],
            "Angular" => ["angular", "angularjs"],
            "Django" => ["django"],
            "Flask" => ["flask"],
            "FastAPI" => ["fastapi"],
            "Tailwind CSS" => ["tailwind"],
            "Bootstrap" => ["bootstrap"],
            "HTML/CSS" => ["html", "css", "html5", "css3"],

            // INFRA (Strict)
            "Git" => ["git ", "github", "gitlab", "bitbucket"],
            "Docker" => ["docker", "containerization"],
            "Kubernetes" => ["kubernetes", "k8s"],
            "AWS" => ["aws", "amazon web services", "ec2", "s3", "lambda"],
            "Azure" => ["azure"],
            "GCP" => ["google cloud platform", "gcp"],
            "Linux" => ["linux", "ubuntu", "centos", "bash shell"],

            // AI / DATA (Strict)
            "Machine Learning" => ["machine learning", "scikit-learn", "sklearn"],
            "Deep Learning" => ["deep learning", "tensorflow", "pytorch", "keras"],
            "Data Analysis" => ["data analysis", "pandas", "numpy", "matplotlib"],
            "AI" => ["artificial intelligence", "generative ai", "llm", "nlp", "computer vision"],
            "SQL" => ["sql", "mysql", "postgresql", "postgres", "sqlite"],
            "NoSQL" => ["mongodb", "redis", "cassandra"],

            // --- UNIVERSAL / NON-IT SKILLS (ALWAYS SAFE) ---
            
            // FINANCE & ACCOUNTING (EXPANDED)
            "Financial Analysis" => ["financial analysis", "analisis keuangan", "financial modeling", "financial reporting", "laporan keuangan"],
            "Accounting" => ["accounting", "akuntansi", "general ledger", "reconciliation", "neraca", "rugi laba", "psak", "ifrs"],
            "Taxation" => ["tax", "perpajakan", "brevet", "pajak", "efaktur", "espt"],
            "Auditing" => ["audit", "internal audit", "external audit"],
            "Banking" => ["banking", "perbankan", "credit analysis", "treasury"],
            "MYOB/Zahir/Accurate" => ["myob", "zahir", "accurate", "sap fico"],

            // MARKETING & SALES (EXPANDED)
            "Digital Marketing" => ["digital marketing", "seo", "sem", "google ads", "facebook ads", "meta ads", "performance marketing"],
            "Social Media Management" => ["social media", "instagram", "tiktok", "content strategy", "social media strategy", "community manager"],
            "Content Creation" => ["content creation", "copywriting", "content writing", "storytelling", "scriptwriting", "blogging"],
            "Market Research" => ["market research", "riset pasar", "competitor analysis", "consumer behavior"],
            "Sales & Negotiation" => ["sales", "penjualan", "negotiation", "negosiasi", "account management", "b2b sales", "prospecting"],
            "CRM" => ["crm", "customer relationship", "hubspot", "salesforce", "zoho"],
            
            // OFFICE & ADMIN (EXPANDED)
            "Microsoft Office" => ["microsoft office", "ms office", "word", "powerpoint", "outlook"],
            "Excel (Advanced)" => ["excel", "vlookup", "hlookup", "pivot table", "macro", "vba", "spreadsheet"],
            "Administration" => ["administration", "administrasi", "filing", "archiving", "data entry", "receptionist", "secretary", "scheduling"],
            "Google Workspace" => ["google workspace", "google docs", "google sheets", "google slides"],

            // DESIGN & CREATIVE (EXPANDED)
            "Graphic Design" => ["graphic design", "desain grafis", "layouting", "visual identity"],
            "UI/UX Design" => ["ui/ux", "user interface", "user experience", "wireframing", "prototyping"],
            "Figma" => ["figma"],
            "Adobe Creative Suite" => ["adobe", "photoshop", "illustrator", "indesign", "premiere pro", "effects", "lightroom"],
            "Video Editing" => ["video editing", "video production", "capcut", "final cut", "davinci resolve"],
            "Photography" => ["photography", "fotografi", "photo editing"],

            // SOFT SKILLS & MANAGEMENT (EXPANDED)
            "Leadership" => ["leadership", "kepemimpinan", "team leader", "organisasi", "organization", "ketua"],
            "Communication" => ["communication", "komunikasi", "public speaking", "presentasi", "presentation"],
            "Teamwork" => ["teamwork", "kerjasama tim", "collaboration", "kolaborasi"],
            "Problem Solving" => ["problem solving", "critical thinking", "analytical thinking", "pemecahan masalah"],
            "Project Management" => ["project management", "manajemen proyek", "agile", "scrum", "kanban", "trello", "jira", "asana"],
            "Time Management" => ["time management", "manajemen waktu", "prioritization", "multitasking"],
            "English Language" => ["english", "inggris", "toefl", "ielts", "toeic", "speaking", "writing"],
            "Bahasa Indonesia" => ["bahasa indonesia", "ejaan", "puebi"]
        ];

        $detectedSkills = [];

        foreach ($skillMap as $mainSkill => $keywords) {
            // Context Check for IT skills
            // If the skill seems "Techy" (Programming, Infra, AI) but Context is NOT Tech, be ultra strict or skip
            $isTechSkill = in_array($mainSkill, ["Python", "Java", "C++", "C#", "PHP", "JavaScript", "TypeScript", "Go", "Ruby", "Swift", "Kotlin", "Rust", "Dart", "R", "React", "Vue", "Angular", "Django", "Flask", "Tailwind CSS", "Bootstrap", "Linux", "Terraform", "Docker", "Kubernetes", "AWS", "Azure", "GCP", "Machine Learning", "Deep Learning"]);
            
            if ($isTechSkill && !$isTechContext) {
                 // Skip dangerous short keywords if not in tech context
                 continue; 
            }

            foreach ($keywords as $kw) {
                if (strpos($lowerText, $kw) !== false) {
                    $detectedSkills[] = $mainSkill;
                    break;
                }
            }
        }

        return array_values(array_unique($detectedSkills));
    }

    private function extractEducation($text)
    {
        $text = strtolower($text);
        $edu = 'S1 (Sarjana)'; 
        
        // 1. Detect Level
        if (strpos($text, 's3') !== false || strpos($text, 'doktor') !== false) $edu = 'S3 (Doktor)';
        elseif (strpos($text, 's2') !== false || strpos($text, 'magister') !== false) $edu = 'S2 (Magister)';
        elseif (strpos($text, 's1') !== false || strpos($text, 'sarjana') !== false || strpos($text, 'bachelor') !== false) $edu = 'S1 (Sarjana)';
        elseif (strpos($text, 'diploma') !== false || strpos($text, 'd3') !== false || strpos($text, 'd4') !== false) $edu = 'Diploma (D3/D4)';
        elseif (strpos($text, 'smk') !== false) $edu = 'SMK';
        elseif (strpos($text, 'sma') !== false) $edu = 'SMA';
        
        // 2. Smart Inference (If degree not explicit, guess by major)
        if ($edu == 'S1 (Sarjana)') { 
             if (strpos($text, 'informatika') !== false || strpos($text, 'komunikasi') !== false || strpos($text, 'psikologi') !== false || strpos($text, 'hukum') !== false || strpos($text, 'akuntansi') !== false) {
                 // S1
             } elseif (strpos($text, 'administrasi') !== false) {
                 // Could be D3, but stick to S1 default if not explicit D3 mentioned
             }
        }

        // 3. Extract IPK / GPA
        if (preg_match('/ipk\s*[:=]?\s*(\d[\.,]\d{1,2})/', $text, $matches) || preg_match('/gpa\s*[:=]?\s*(\d[\.,]\d{1,2})/', $text, $matches)) {
            $ipk = str_replace(',', '.', $matches[1]);
            if (floatval($ipk) <= 4.00) {
                $edu .= " | IPK: $ipk";
            }
        }

        return $edu;
    }

    private function estimateExperience($text)
    {
        $totalYears = 0.5; 
        preg_match_all('/20\d{2}\s*-\s*20\d{2}/', $text, $matches);
        $count = count($matches[0]);
        
        if ($count > 0) {
            $totalYears = $count * 0.8; 
        }
        
        if (preg_match('/(\d+)\s*(tahun|year)/i', $text, $m)) {
            $val = floatval($m[1]);
            if ($val > $totalYears) $totalYears = $val;
        }

        if ($totalYears > 5) $totalYears = 5; 
        return round($totalYears, 1);
    }
}
