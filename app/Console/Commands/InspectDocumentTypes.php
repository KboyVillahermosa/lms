<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DocumentType;

class InspectDocumentTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspect:document-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Print document types and accepted_formats for debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $types = DocumentType::all();
        foreach ($types as $t) {
            $this->line("ID: {$t->id} | slug: {$t->slug} | accepted_formats: " . json_encode($t->accepted_formats));
        }

        return 0;
    }
}
