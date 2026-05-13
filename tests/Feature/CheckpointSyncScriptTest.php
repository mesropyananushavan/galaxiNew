<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckpointSyncScriptTest extends TestCase
{
    public function test_checkpoint_sync_script_passes_when_checkpoint_files_are_clean(): void
    {
        $result = $this->runScript();

        $this->assertSame(0, $result['exitCode']);
        $this->assertStringContainsString('Checkpoint docs are clean', $result['output']);
    }

    public function test_checkpoint_sync_script_fails_when_project_status_is_dirty(): void
    {
        $statusPath = base_path('shared/PROJECT_STATUS.json');
        $original = file_get_contents($statusPath);

        $this->assertNotFalse($original);

        $updated = preg_replace(
            '/"updated_at":\s*"[^"]+"/',
            '"updated_at": "2099-01-01T00:00:00Z"',
            $original,
            1,
        );

        $this->assertIsString($updated);
        file_put_contents($statusPath, $updated);

        try {
            $result = $this->runScript();

            $this->assertSame(2, $result['exitCode']);
            $this->assertStringContainsString('Checkpoint docs changed:', $result['output']);
            $this->assertStringContainsString('shared/PROJECT_STATUS.json', $result['output']);
        } finally {
            file_put_contents($statusPath, $original);
        }
    }

    /**
     * @return array{exitCode:int, output:string}
     */
    private function runScript(): array
    {
        $command = 'bash scripts/checkpoint-sync.sh 2>&1';
        $output = [];
        $exitCode = 0;

        exec('cd '.escapeshellarg(base_path()).' && '.$command, $output, $exitCode);

        return [
            'exitCode' => $exitCode,
            'output' => implode("\n", $output),
        ];
    }
}
