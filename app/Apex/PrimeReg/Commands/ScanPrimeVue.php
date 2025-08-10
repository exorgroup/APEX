<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Command to scan PrimeVue components and generate curated API registry
 * File location: app/Apex/PrimeReg/Commands/ScanPrimeVue.php
 */

namespace App\Apex\PrimeReg\Commands;

use App\Apex\PrimeReg\Services\ComponentScanner;
use App\Apex\PrimeReg\Services\CurationValidator;
use App\Apex\PrimeReg\Services\RegistryGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ScanPrimeVue extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'apex:scan-primevue 
                            {--component= : Specific component to scan}
                            {--curate : Process curated component data}
                            {--output= : Custom output location}
                            {--all : Scan all available components}';

    /**
     * The console command description.
     */
    protected $description = 'Scan PrimeVue components and generate curated API registry for APEX framework';

    /**
     * Component scanner service instance
     */
    private ComponentScanner $scanner;

    /**
     * Curation validator service instance
     */
    private CurationValidator $validator;

    /**
     * Registry generator service instance
     */
    private RegistryGenerator $generator;

    /**
     * Create a new command instance.
     *
     * @param ComponentScanner $scanner Service for scanning component APIs
     * @param CurationValidator $validator Service for validating curation files
     * @param RegistryGenerator $generator Service for generating final registry
     */
    public function __construct(
        ComponentScanner $scanner,
        CurationValidator $validator,
        RegistryGenerator $generator
    ) {
        try {
            parent::__construct();
            $this->scanner = $scanner;
            $this->validator = $validator;
            $this->generator = $generator;
        } catch (\Exception $e) {
            Log::error('Error in ScanPrimeVue constructor', [
                'folder' => 'app/Apex/PrimeReg/Commands',
                'file' => 'ScanPrimeVue.php',
                'method' => '__construct',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Execute the console command.
     *
     * @return int Command exit code (0 for success, 1 for error)
     */
    public function handle(): int
    {
        try {
            $this->info('🚀 APEX PrimeVue Component Scanner');
            $this->line('=====================================');

            $component = $this->option('component');
            $curate = $this->option('curate');
            $scanAll = $this->option('all');

            if ($scanAll) {
                return $this->scanAllComponents();
            }

            if ($component) {
                if ($curate) {
                    return $this->curateComponent($component);
                } else {
                    return $this->scanComponent($component);
                }
            }

            $this->error('❌ Please specify --component, --all, or use --curate with --component');
            $this->line('');
            $this->line('Examples:');
            $this->line('  php artisan apex:scan-primevue --component=inputtext');
            $this->line('  php artisan apex:scan-primevue --component=inputtext --curate');
            $this->line('  php artisan apex:scan-primevue --all');

            return 1;
        } catch (\Exception $e) {
            Log::error('Error in ScanPrimeVue handle method', [
                'folder' => 'app/Apex/PrimeReg/Commands',
                'file' => 'ScanPrimeVue.php',
                'method' => 'handle',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('❌ An error occurred: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Scan a single component and generate curation file
     *
     * @param string $componentName Name of the component to scan
     * @return int Command exit code
     */
    private function scanComponent(string $componentName): int
    {
        try {
            $this->info("🔍 Scanning component: {$componentName}");

            // Check if PrimeVue is installed
            if (!$this->scanner->isPrimeVueInstalled()) {
                $this->error('❌ PrimeVue not found in node_modules');
                $this->line('Please run: npm install primevue');
                return 1;
            }

            // Scan the component
            $rawData = $this->scanner->scanComponent($componentName);

            if (empty($rawData)) {
                $this->error("❌ Component '{$componentName}' not found or could not be scanned");
                return 1;
            }

            // Generate curation file
            $curationFile = $this->scanner->generateCurationFile($componentName, $rawData);

            $this->info("✅ Curation file generated: {$curationFile}");
            $this->line('');
            $this->warn('📝 Next steps:');
            $this->line('1. Edit the curation file to assign features to tiers (core/pro/enterprise)');
            $this->line('2. Add proper descriptions for each property/event');
            $this->line('3. Run with --curate flag to generate final registry');
            $this->line('');
            $this->line("Command: php artisan apex:scan-primevue --component={$componentName} --curate");

            return 0;
        } catch (\Exception $e) {
            Log::error('Error scanning component', [
                'folder' => 'app/Apex/PrimeReg/Commands',
                'file' => 'ScanPrimeVue.php',
                'method' => 'scanComponent',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('❌ Error scanning component: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Process curated component data and generate final registry
     *
     * @param string $componentName Name of the component to curate
     * @return int Command exit code
     */
    private function curateComponent(string $componentName): int
    {
        try {
            $this->info("⚗️ Processing curated component: {$componentName}");

            // Check if curation file exists
            $curationFile = storage_path("app/apex/curation/{$componentName}.json");

            if (!file_exists($curationFile)) {
                $this->error('❌ Curation file not found');
                $this->line("Expected: {$curationFile}");
                $this->line("Run without --curate first to generate the curation file");
                return 1;
            }

            // Load and validate curation data
            $curatedData = json_decode(file_get_contents($curationFile), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('❌ Invalid JSON in curation file: ' . json_last_error_msg());
                return 1;
            }

            // Validate curation
            $validationResult = $this->validator->validate($curatedData);

            if (!$validationResult['valid']) {
                $this->error('❌ Curation validation failed:');
                foreach ($validationResult['errors'] as $error) {
                    $this->line("  • {$error}");
                }
                return 1;
            }

            $this->info('✅ Curation validation passed');

            // Generate final registry
            $registryFiles = $this->generator->generateRegistry($componentName, $curatedData);

            $this->info('✅ Final registry generated successfully!');
            $this->line('');
            $this->info('📁 Generated files:');
            foreach ($registryFiles as $file) {
                $this->line("  • {$file}");
            }

            return 0;
        } catch (\Exception $e) {
            Log::error('Error curating component', [
                'folder' => 'app/Apex/PrimeReg/Commands',
                'file' => 'ScanPrimeVue.php',
                'method' => 'curateComponent',
                'component' => $componentName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('❌ Error curating component: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Scan all available PrimeVue components
     *
     * @return int Command exit code
     */
    private function scanAllComponents(): int
    {
        try {
            $this->info('🔍 Scanning all PrimeVue components...');

            // Check if PrimeVue is installed
            if (!$this->scanner->isPrimeVueInstalled()) {
                $this->error('❌ PrimeVue not found in node_modules');
                $this->line('Please run: npm install primevue');
                return 1;
            }

            // Get list of all components
            $components = $this->scanner->getAllComponents();

            if (empty($components)) {
                $this->error('❌ No components found');
                return 1;
            }

            $this->info("Found {count($components)} components");
            $this->line('');

            $successCount = 0;
            $errorCount = 0;

            foreach ($components as $component) {
                $this->line("Scanning: {$component}");

                try {
                    $rawData = $this->scanner->scanComponent($component);

                    if (!empty($rawData)) {
                        $this->scanner->generateCurationFile($component, $rawData);
                        $this->info("  ✅ {$component} - curation file generated");
                        $successCount++;
                    } else {
                        $this->warn("  ⚠️ {$component} - no data found");
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    $this->error("  ❌ {$component} - error: " . $e->getMessage());
                    $errorCount++;
                }
            }

            $this->line('');
            $this->info("📊 Scan complete:");
            $this->line("  • Successful: {$successCount}");
            $this->line("  • Errors: {$errorCount}");
            $this->line('');
            $this->warn('📝 Next steps:');
            $this->line('1. Review and edit each curation file in storage/app/apex/curation/');
            $this->line('2. Assign features to appropriate tiers');
            $this->line('3. Run with --curate for each component to generate registries');

            return $errorCount > 0 ? 1 : 0;
        } catch (\Exception $e) {
            Log::error('Error scanning all components', [
                'folder' => 'app/Apex/PrimeReg/Commands',
                'file' => 'ScanPrimeVue.php',
                'method' => 'scanAllComponents',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->error('❌ Error scanning components: ' . $e->getMessage());
            return 1;
        }
    }
}
