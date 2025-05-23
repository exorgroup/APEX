<?php

namespace App\Http\Middleware;

use App\Apex\TemplateProcessor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ApexTemplateMiddleware
{
    protected TemplateProcessor $processor;

    public function __construct(TemplateProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only process HTML responses
        if (!$this->shouldProcessResponse($response)) {
            return $response;
        }

        try {
            $content = $response->getContent();

            // Check if content contains APEX tags
            if ($this->containsApexTags($content)) {
                $processedContent = $this->processor->process($content);
                $response->setContent($processedContent);

                // Add header to indicate processing occurred
                $response->headers->set('X-Apex-Processed', 'true');

                Log::debug('APEX: Template processed successfully', [
                    'widgets_count' => count($this->processor->getProcessedWidgets()),
                    'url' => $request->url()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('APEX: Template processing failed', [
                'error' => $e->getMessage(),
                'url' => $request->url(),
                'trace' => $e->getTraceAsString()
            ]);

            // In production, return original content on error
            if (!config('app.debug', false)) {
                return $response;
            }

            // In debug mode, show error
            $errorContent = $this->renderProcessingError($e, $response->getContent());
            $response->setContent($errorContent);
        }

        return $response;
    }

    /**
     * Determine if the response should be processed
     */
    protected function shouldProcessResponse($response): bool
    {
        // Must be a successful response
        if (!$response instanceof Response) {
            return false;
        }

        // Must be HTML content
        $contentType = $response->headers->get('Content-Type', '');
        if (strpos($contentType, 'text/html') === false && empty($contentType)) {
            return false;
        }

        // Skip if already processed
        if ($response->headers->has('X-Apex-Processed')) {
            return false;
        }

        // Skip if explicitly disabled
        if ($response->headers->has('X-Apex-No-Process')) {
            return false;
        }

        // Skip AJAX requests by default (can be overridden)
        if (request()->ajax() && !config('apex.process_ajax', false)) {
            return false;
        }

        return true;
    }

    /**
     * Check if content contains APEX tags
     */
    protected function containsApexTags(string $content): bool
    {
        return strpos($content, '!!apex-') !== false;
    }

    /**
     * Render processing error (debug mode only)
     */
    protected function renderProcessingError(\Exception $e, string $originalContent): string
    {
        $errorHtml = '
        <div style="
            background: #ff6b6b; 
            color: white; 
            padding: 20px; 
            margin: 20px; 
            border-radius: 5px;
            font-family: monospace;
            border: 2px solid #ff5252;
            z-index: 9999;
            position: relative;
        ">
            <h3 style="margin: 0 0 10px 0;">APEX Template Processing Error</h3>
            <p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
            <p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>
            <details style="margin-top: 10px;">
                <summary style="cursor: pointer;">Stack Trace</summary>
                <pre style="
                    background: rgba(0,0,0,0.2); 
                    padding: 10px; 
                    margin: 10px 0; 
                    border-radius: 3px;
                    white-space: pre-wrap;
                    font-size: 12px;
                ">' . htmlspecialchars($e->getTraceAsString()) . '</pre>
            </details>
        </div>';

        // Insert error at the beginning of body or return with original content
        if (strpos($originalContent, '<body') !== false) {
            return str_replace('<body', $errorHtml . '<body', $originalContent);
        }

        return $errorHtml . $originalContent;
    }
}
