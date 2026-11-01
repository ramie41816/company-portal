<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class TemplateManagerController extends Controller
{
    /**
     * Scan the templates directory and group files by category (e.g., emails, pdf).
     */
    private function getSidebarTemplates()
    {
        $templatesDirectory = resource_path('views/templates');
        $sidebarGroups = [];

        if (File::exists($templatesDirectory)) {
            $directories = File::directories($templatesDirectory);

            foreach ($directories as $dir) {
                $categoryName = basename($dir);
                $sidebarGroups[$categoryName] = [];

                $files = File::files($dir);
                foreach ($files as $file) {
                    $filename = $file->getFilename();
                    if (str_ends_with($filename, '.blade.php')) {
                        $templateName = str_replace('.blade.php', '', $filename);
                        $sidebarGroups[$categoryName][$templateName] = "templates.{$categoryName}.{$templateName}";
                    }
                }
            }
        }

        return $sidebarGroups;
    }

    /**
     * Display the Template Viewer Dashboard.
     */
    public function index(Request $request)
    {
        $groupedTemplates = $this->getSidebarTemplates();
        $viewPath = $request->input('view') ?? "";

        // Automatically determine type from viewPath if not provided (e.g., templates.pdf.quotation -> pdf)
        $type = $request->input('type');
        if (!$type && !empty($viewPath)) {
            $segments = explode('.', $viewPath);
            $type = $segments[1] ?? ''; // Grabs 'pdf' or 'emails' from 'templates.pdf.name'
        }
        $type = strtolower($type);

        return view('templates.index', [
            'groupedTemplates' => $groupedTemplates,
            'viewPath' => $viewPath,
            'type' => $type
        ]);
    }

    /**
     * Render the specific raw template inside the iframe (for emails/HTML).
     */
    public function render(Request $request)
    {
        $viewPath = $request->input('view');

        if (!$viewPath || !str_starts_with($viewPath, 'templates.') || !view()->exists($viewPath)) {
            abort(404, 'Template not found or unauthorized.');
        }

        return view($viewPath, [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Stream the PDF template directly.
     */
    public function streamPdf(Request $request)
    {
        $viewPath = $request->input('view');

        if (!$viewPath || !str_starts_with($viewPath, 'templates.') || !view()->exists($viewPath)) {
            abort(404, 'Template not found or unauthorized.');
        }

        // Compiles and streams the template view using Dompdf
        $pdf = Pdf::loadView($viewPath, [
            'user' => auth()->user(),
        ]);

        return $pdf->stream('template-preview.pdf');
    }
}
