# Graph Report - applyinternshipioh_new  (2026-08-29)

## Corpus Check
- 324 files · ~300,078 words
- Verdict: corpus is large enough that graph structure adds value.

## Summary
- 662 nodes · 948 edges · 128 communities (105 shown, 23 thin omitted)
- Extraction: 97% EXTRACTED · 3% INFERRED · 0% AMBIGUOUS · INFERRED: 26 edges (avg confidence: 0.84)
- Token cost: 1,500 input · 500 output

## Community Hubs (Navigation)
- Admin Controller & Authentication
- Data Retention & Archiving Task
- FPDF Engine & Font Parsing (2)
- FPDF Engine & Font Parsing (3)
- FPDF Engine & Font Parsing (4)
- Core MVC Controller Infrastructure
- Database Migrations & Seeders
- Database Migrations & Seeders
- Admin Controller & Authentication
- FPDF Engine & Font Parsing (11)
- FPDF Engine & Font Parsing (12)
- AI CV Analysis Engine
- Certificate Generation Service
- Paths
- Cache
- Toolbar
- tuto4
- Exceptions
- Admin Controller & Authentication
- tuto3
- tuto6
- Config/Validation
- Proposal AI Analysis Engine
- Database Migrations & Seeders
- debug.js
- Filters
- InternshipLocations
- Database Migrations & Seeders
- SupabaseStorage
- Database
- Events
- Format
- Images
- Logger
- Mimes
- tuto2
- Certificate Generation System
- Autoload
- ForeignCharacters
- Modules
- Publisher
- Routing
- html/error exception
- DocTypes
- Optimize
- Routes
- custom full

## God Nodes (most connected - your core abstractions)
1. `FPDF` - 96 edges
2. `Admin` - 38 edges
3. `TTFParser` - 36 edges
4. `Session` - 18 edges
5. `CvAnalysisService` - 13 edges
6. `Pendaftaran` - 12 edges
7. `PendaftaranModel` - 12 edges
8. `Progres` - 10 edges
9. `MakeFont()` - 9 edges
10. `PDF` - 9 edges

## Surprising Connections (you probably didn't know these)
- `Certificate Generation System` --PART_OF--> `Indosat Internship Portal`  [INFERRED]
  app/Services/CertificateService.php → README.md
- `ConfigReader` --inherits--> `App`  [EXTRACTED]
  tests/_support/Libraries/ConfigReader.php → app/Config/App.php
- `Admin` --inherits--> `BaseController`  [EXTRACTED]
  app/Controllers/Admin.php → app/Controllers/BaseController.php
- `PDF` --inherits--> `FPDF`  [EXTRACTED]
  app/ThirdParty/fpdf/tutorial/tuto2.php → app/ThirdParty/fpdf/fpdf.php
- `PDF` --inherits--> `FPDF`  [EXTRACTED]
  app/ThirdParty/fpdf/tutorial/tuto3.php → app/ThirdParty/fpdf/fpdf.php

## Import Cycles
- None detected.

## Communities (128 total, 23 thin omitted)

### Community 0 - "Admin Controller & Authentication"
Cohesion: 0.09
Nodes (7): Session, View, Admin, CodeIgniter\Config\View, CodeIgniter\Session\Handlers\BaseHandler, CodeIgniter\Session\Handlers\FileHandler, CodeIgniter\View\ViewDecoratorInterface

### Community 1 - "Data Retention & Archiving Task"
Cohesion: 0.07
Nodes (19): Cleanuppendaftaran, CsvImport, Services, AdminModel, AppSettingsModel, PendaftaranModel, CodeIgniter\CLI\BaseCommand, CodeIgniter\CLI\CLI (+11 more)

### Community 2 - "FPDF Engine & Font Parsing (2)"
Cohesion: 0.05
Nodes (37): autoload, autoload-dev, psr-4, exclude-from-classmap, psr-4, config, optimize-autoloader, preferred-install (+29 more)

### Community 5 - "Core MVC Controller Infrastructure"
Cohesion: 0.09
Nodes (10): BaseController, RequestInterface, ResponseInterface, Home, Pendaftaran, Progres, Sitemap, CodeIgniter\API\ResponseTrait (+2 more)

### Community 6 - "Database Migrations & Seeders"
Cohesion: 0.11
Nodes (15): ContentSecurityPolicy, Cookie, Cors, CURLRequest, Email, Encryption, Feature, Generators (+7 more)

### Community 7 - "Database Migrations & Seeders"
Cohesion: 0.09
Nodes (8): CreateAppSettingsTable, AddPendaftaranUniqueApplicantIndexes, AddKotaMagangToPendaftaran, RenameKotaMagangToKotaPilihan, ChangeStatusToVarchar, CodeIgniter\Database\Migration, RuntimeException, ExampleMigration

### Community 8 - "Admin Controller & Authentication"
Cohesion: 0.11
Nodes (10): App, AdminUserSeeder, CodeIgniter\Database\Seeder, CodeIgniter\Test\CIUnitTestCase, CodeIgniter\Test\DatabaseTestTrait, ExampleDatabaseTest, ExampleSessionTest, ExampleSeeder (+2 more)

### Community 11 - "FPDF Engine & Font Parsing (11)"
Cohesion: 0.12
Nodes (15): authors, autoload, classmap, description, homepage, keywords, license, name (+7 more)

### Community 12 - "FPDF Engine & Font Parsing (12)"
Cohesion: 0.33
Nodes (14): ConvertToJSON(), Error(), GetEncodingDiff(), GetFontDescriptor(), GetInfoFromTrueType(), GetInfoFromType1(), GetUnicodeMapping(), LoadMap() (+6 more)

### Community 14 - "Certificate Generation Service"
Cohesion: 0.24
Nodes (4): CertificateService, DOMDocument, DOMXPath, ZipArchive

### Community 15 - "Paths"
Cohesion: 0.24
Nodes (3): Paths, CodeIgniter\Boot, preload

### Community 16 - "Cache"
Cohesion: 0.22
Nodes (8): Cache, CodeIgniter\Cache\CacheInterface, CodeIgniter\Cache\Handlers\DummyHandler, CodeIgniter\Cache\Handlers\FileHandler, CodeIgniter\Cache\Handlers\MemcachedHandler, CodeIgniter\Cache\Handlers\PredisHandler, CodeIgniter\Cache\Handlers\RedisHandler, CodeIgniter\Cache\Handlers\WincacheHandler

### Community 17 - "Toolbar"
Cohesion: 0.22
Nodes (8): Toolbar, CodeIgniter\Debug\Toolbar\Collectors\Database, CodeIgniter\Debug\Toolbar\Collectors\Events, CodeIgniter\Debug\Toolbar\Collectors\Files, CodeIgniter\Debug\Toolbar\Collectors\Logs, CodeIgniter\Debug\Toolbar\Collectors\Routes, CodeIgniter\Debug\Toolbar\Collectors\Timers, CodeIgniter\Debug\Toolbar\Collectors\Views

### Community 19 - "Exceptions"
Cohesion: 0.38
Nodes (5): Exceptions, CodeIgniter\Debug\ExceptionHandler, CodeIgniter\Debug\ExceptionHandlerInterface, Psr\Log\LogLevel, Throwable

### Community 20 - "Admin Controller & Authentication"
Cohesion: 0.48
Nodes (4): AdminAuth, CodeIgniter\Filters\FilterInterface, CodeIgniter\HTTP\RequestInterface, CodeIgniter\HTTP\ResponseInterface

### Community 24 - "Config/Validation"
Cohesion: 0.33
Nodes (5): Validation, CodeIgniter\Validation\StrictRules\CreditCardRules, CodeIgniter\Validation\StrictRules\FileRules, CodeIgniter\Validation\StrictRules\FormatRules, CodeIgniter\Validation\StrictRules\Rules

### Community 27 - "debug.js"
Cohesion: 0.53
Nodes (4): getFirstChildWithTagName(), getHash(), init(), showTab()

### Community 28 - "Filters"
Cohesion: 0.40
Nodes (4): Filters, CodeIgniter\Filters\CSRF, CodeIgniter\Filters\DebugToolbar, CodeIgniter\Filters\Honeypot

### Community 30 - "Database Migrations & Seeders"
Cohesion: 0.40
Nodes (4): Kint, Kint\Parser\ConstructablePluginInterface, Kint\Renderer\Rich\TabPluginInterface, Kint\Renderer\Rich\ValuePluginInterface

### Community 35 - "Events"
Cohesion: 0.50
Nodes (3): CodeIgniter\Events\Events, CodeIgniter\Exceptions\FrameworkException, CodeIgniter\HotReloader\HotReloader

### Community 36 - "Format"
Cohesion: 0.50
Nodes (3): Format, CodeIgniter\Format\JSONFormatter, CodeIgniter\Format\XMLFormatter

### Community 37 - "Images"
Cohesion: 0.50
Nodes (3): Images, CodeIgniter\Images\Handlers\GDHandler, CodeIgniter\Images\Handlers\ImageMagickHandler

### Community 38 - "Logger"
Cohesion: 0.50
Nodes (3): Logger, CodeIgniter\Log\Handlers\FileHandler, CodeIgniter\Log\Handlers\HandlerInterface

### Community 42 - "Certificate Generation System"
Cohesion: 0.67
Nodes (3): Certificate Generation System, README, Indosat Internship Portal

## Knowledge Gaps
- **43 isolated node(s):** `DocTypes`, `Kint`, `Optimize`, `name`, `homepage` (+38 more)
  These have ≤1 connection - possible missing edges or undocumented components.
- **23 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `Session` connect `Admin Controller & Authentication` to `Data Retention & Archiving Task`, `Admin Controller & Authentication`, `Database Migrations & Seeders`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._
- **Why does `FPDF` connect `FPDF Engine & Font Parsing (4)` to `.AddFont()`, `. parsegif()`, `. httpencode()`, `. enddoc()`, `.AcceptPageBreak()`, `tuto2`, `tuto4`, `.AddPage()`, `tuto3`, `tuto6`, `Database Migrations & Seeders`?**
  _High betweenness centrality (0.033) - this node is a cross-community bridge._
- **Why does `Admin` connect `Admin Controller & Authentication` to `Data Retention & Archiving Task`, `Core MVC Controller Infrastructure`?**
  _High betweenness centrality (0.032) - this node is a cross-community bridge._
- **Are the 16 inferred relationships involving `Session` (e.g. with `.analyzeProposal()` and `.dashboard()`) actually correct?**
  _`Session` has 16 INFERRED edges - model-reasoned connections that need verification._
- **What connects `DocTypes`, `Kint`, `Optimize` to the rest of the system?**
  _43 weakly-connected nodes found - possible documentation gaps or missing edges._
- **Should `Admin Controller & Authentication` be split into smaller, more focused modules?**
  _Cohesion score 0.08502415458937199 - nodes in this community are weakly interconnected._
- **Should `Data Retention & Archiving Task` be split into smaller, more focused modules?**
  _Cohesion score 0.07422402159244265 - nodes in this community are weakly interconnected._