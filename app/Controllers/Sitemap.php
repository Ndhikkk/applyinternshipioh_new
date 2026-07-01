<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Sitemap extends Controller
{
    public function index()
    {
        $response = service('response');
        $response->setContentType('application/xml');

        $baseUrl = base_url();
        $today = date('Y-m-d');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Home Page
        $xml .= '<url>';
        $xml .= "<loc>{$baseUrl}</loc>";
        $xml .= "<lastmod>{$today}</lastmod>";
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';

        // Pendaftaran Page
        $xml .= '<url>';
        $xml .= "<loc>{$baseUrl}pendaftaran</loc>";
        $xml .= "<lastmod>{$today}</lastmod>";
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';

        // Cek Progres Page
        $xml .= '<url>';
        $xml .= "<loc>{$baseUrl}progres</loc>";
        $xml .= "<lastmod>{$today}</lastmod>";
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '<priority>0.7</priority>';
        $xml .= '</url>';
        
        // Login Page (Optional but good for indexing existance)
        $xml .= '<url>';
        $xml .= "<loc>{$baseUrl}admin/login</loc>";
        $xml .= "<lastmod>{$today}</lastmod>";
        $xml .= '<changefreq>monthly</changefreq>';
        $xml .= '<priority>0.5</priority>';
        $xml .= '</url>';

        $xml .= '</urlset>';

        return $response->setBody($xml);
    }
}
