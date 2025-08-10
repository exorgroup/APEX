<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Diagnostic scanner to understand PrimeVue structure and find actual files
 * File location: app/Apex/PrimeReg/Commands/DiagnosePrimeVue.php
 */

namespace App\Apex\PrimeReg\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DiagnosePrimeVue extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'apex:diagnose-primevue {--component=inputtext}';

    /**
     * The console command description.
     */
    protected $description = 'Diagnose PrimeVue installation and file structure';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $componentName = $this->option('component');

            $this->info("🔍 Diagnosing PrimeVue for component: {$componentName}");
            $this->line('=====================================');

            // Check basic installation
            $this->checkPrimeVueInstallation();

            // Explore directory structure
            $this->exploreDirectoryStructure($componentName);

            // Look for TypeScript files
            $this->findTypeScriptFiles($componentName);

            // Check package.json
            $this->checkPackageJson();

            // Look for Vue files
            $this->findVueFiles($componentName);

            // Show actual file contents
            $this->showFileContents($componentName);

            return 0;
        } catch (\Exception $e) {
            Log::error('Error in DiagnosePrimeVue', [
                'folder' => 'app/Apex/PrimeReg/Commands',
                'file' => 'DiagnosePrimeVue.php',
                'method' => 'handle',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if PrimeVue is properly installed
     */
    private function checkPrimeVueInstallation(): void
    {
        try {
            $primeVuePath = base_path('node_modules/primevue');

            $this->line('📦 PrimeVue Installation Check:');

            if (!File::exists($primeVuePath)) {
                $this->error("❌ PrimeVue not found at: {$primeVuePath}");
                return;
            }

            $this->info("✅ PrimeVue found at: {$primeVuePath}");

            // Check package.json for version
            $packageJsonPath = "{$primeVuePath}/package.json";
            if (File::exists($packageJsonPath)) {
                $packageJson = json_decode(File::get($packageJsonPath), true);
                $version = $packageJson['version'] ?? 'unknown';
                $this->info("📋 Version: {$version}");
            }

            $this->line('');
        } catch (\Exception $e) {
            $this->error('Error checking installation: ' . $e->getMessage());
        }
    }

    /**
     * Explore the directory structure for a component
     */
    private function exploreDirectoryStructure(string $componentName): void
    {
        try {
            $primeVuePath = base_path('node_modules/primevue');

            $this->line("📁 Directory Structure for '{$componentName}':");

            // Check component directory
            $componentPath = "{$primeVuePath}/{$componentName}";

            if (!File::exists($componentPath)) {
                $this->warn("⚠️ Component directory not found: {$componentPath}");

                // List all available components
                $this->line('Available components:');
                $dirs = File::directories($primeVuePath);
                foreach ($dirs as $dir) {
                    $name = basename($dir);
                    if (!in_array($name, ['core', 'themes', 'icons', 'utils', 'config'])) {
                        $this->line("  • {$name}");
                    }
                }
                return;
            }

            $this->info("✅ Component directory found: {$componentPath}");

            // List all files in component directory
            $this->listFilesRecursively($componentPath, '  ');
            $this->line('');
        } catch (\Exception $e) {
            $this->error('Error exploring directory: ' . $e->getMessage());
        }
    }

    /**
     * List files recursively with indentation
     */
    private function listFilesRecursively(string $path, string $indent = ''): void
    {
        try {
            if (!File::exists($path)) {
                return;
            }

            $items = File::glob("{$path}/*");

            foreach ($items as $item) {
                $name = basename($item);

                if (File::isDirectory($item)) {
                    $this->line("{$indent}📁 {$name}/");
                    if (strlen($indent) < 10) { // Limit recursion depth
                        $this->listFilesRecursively($item, $indent . '  ');
                    }
                } else {
                    $size = File::size($item);
                    $sizeFormatted = $this->formatBytes($size);
                    $this->line("{$indent}📄 {$name} ({$sizeFormatted})");
                }
            }
        } catch (\Exception $e) {
            $this->error('Error listing files: ' . $e->getMessage());
        }
    }

    /**
     * Look for TypeScript definition files
     */
    private function findTypeScriptFiles(string $componentName): void
    {
        try {
            $this->line('🔍 TypeScript Files Search:');

            $primeVuePath = base_path('node_modules/primevue');
            $searchPaths = [
                "{$primeVuePath}/{$componentName}",
                "{$primeVuePath}/types",
                "{$primeVuePath}/core",
                "{$primeVuePath}"
            ];

            $foundFiles = [];

            foreach ($searchPaths as $searchPath) {
                if (!File::exists($searchPath)) {
                    continue;
                }

                $tsFiles = File::glob("{$searchPath}/**/*.d.ts");
                foreach ($tsFiles as $file) {
                    $relativePath = str_replace($primeVuePath, '', $file);
                    $foundFiles[] = $relativePath;
                }
            }

            if (empty($foundFiles)) {
                $this->warn('⚠️ No TypeScript definition files found');
            } else {
                $this->info('✅ Found TypeScript files:');
                foreach ($foundFiles as $file) {
                    $this->line("  • {$file}");
                }
            }

            $this->line('');
        } catch (\Exception $e) {
            $this->error('Error finding TypeScript files: ' . $e->getMessage());
        }
    }

    /**
     * Check package.json for more information
     */
    private function checkPackageJson(): void
    {
        try {
            $this->line('📋 Package Information:');

            $primeVuePath = base_path('node_modules/primevue');
            $packageJsonPath = "{$primeVuePath}/package.json";

            if (!File::exists($packageJsonPath)) {
                $this->warn('⚠️ package.json not found');
                return;
            }

            $packageJson = json_decode(File::get($packageJsonPath), true);

            $this->info("📦 Name: " . ($packageJson['name'] ?? 'unknown'));
            $this->info("🏷️ Version: " . ($packageJson['version'] ?? 'unknown'));
            $this->info("📝 Description: " . ($packageJson['description'] ?? 'unknown'));

            if (isset($packageJson['types'])) {
                $this->info("🔧 Types entry: " . $packageJson['types']);
            }

            if (isset($packageJson['typesVersions'])) {
                $this->info("🔧 Types versions: " . json_encode($packageJson['typesVersions']));
            }

            $this->line('');
        } catch (\Exception $e) {
            $this->error('Error checking package.json: ' . $e->getMessage());
        }
    }

    /**
     * Look for Vue component files
     */
    private function findVueFiles(string $componentName): void
    {
        try {
            $this->line('🎨 Vue Component Files:');

            $primeVuePath = base_path('node_modules/primevue');
            $componentPath = "{$primeVuePath}/{$componentName}";

            if (!File::exists($componentPath)) {
                $this->warn('⚠️ Component directory not found');
                return;
            }

            $vueFiles = File::glob("{$componentPath}/**/*.vue");

            if (empty($vueFiles)) {
                $this->warn('⚠️ No Vue files found');
            } else {
                $this->info('✅ Found Vue files:');
                foreach ($vueFiles as $file) {
                    $relativePath = str_replace($primeVuePath, '', $file);
                    $size = File::size($file);
                    $sizeFormatted = $this->formatBytes($size);
                    $this->line("  • {$relativePath} ({$sizeFormatted})");
                }
            }

            $this->line('');
        } catch (\Exception $e) {
            $this->error('Error finding Vue files: ' . $e->getMessage());
        }
    }

    /**
     * Show actual file contents for analysis
     */
    private function showFileContents(string $componentName): void
    {
        try {
            $this->line('📖 File Contents Analysis:');

            $primeVuePath = base_path('node_modules/primevue');
            $componentPath = "{$primeVuePath}/{$componentName}";

            // Look for the main files
            $filesToCheck = [
                "{$componentPath}/" . ucfirst($componentName) . ".d.ts",
                "{$componentPath}/index.d.ts",
                "{$componentPath}/" . ucfirst($componentName) . ".vue",
                "{$componentPath}/index.js",
                "{$primeVuePath}/types/{$componentName}.d.ts"
            ];

            foreach ($filesToCheck as $file) {
                if (File::exists($file)) {
                    $relativePath = str_replace($primeVuePath, '', $file);
                    $this->info("📄 Content of {$relativePath}:");

                    $content = File::get($file);
                    $lines = explode("\n", $content);

                    // Show first 20 lines
                    $previewLines = array_slice($lines, 0, 20);
                    foreach ($previewLines as $i => $line) {
                        $lineNum = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                        $this->line("  {$lineNum}: " . substr($line, 0, 100));
                    }

                    if (count($lines) > 20) {
                        $this->line("  ... (" . (count($lines) - 20) . " more lines)");
                    }

                    $this->line('');
                }
            }
        } catch (\Exception $e) {
            $this->error('Error showing file contents: ' . $e->getMessage());
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
