<?php

namespace App\Console\Commands\Elasticsearch;

use Illuminate\Console\Command;

class Migrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elasticsearch index migration';

    protected $es;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->es = app('es');

        $indices = [Indices\ProjectIndex::class];

        foreach ($indices as $indexClass) {
            $aliasName = $indexClass::getAliasName();
            $this->info('Processing index '.$aliasName);

            if (!$this->es->indices()->exists(['index' => $aliasName])) {
                $this->info('this index doesn\'t exist, creating one');
                $this->createIndex($aliasName, $indexClass);
                $this->info('Created, initiate data');
                $indexClass::rebuild($aliasName);
                $this->info('index rebuild');
                continue;
            }

            try {
                $this->info('this index exists, ready to be updated');
                $this->updateIndex($aliasName, $indexClass);
            } catch (\Exception $e) {
                $this->warn('Update failed, going to rebuild');
                $this->reCreateIndex($aliasName, $indexClass);
            }
            $this->info($aliasName.' succeed');
        }
    }

    protected function createIndex($aliasName, $indexClass)
    {
        $this->es->indices()->create([
            'index' => $aliasName.'_0',
            'body' => [
                'settings' => $indexClass::getSettings(),
                'mappings' => [
                    '_doc' => [
                        'properties' => $indexClass::getProperties(),
                    ],
                ],
                'aliases' => [
                    $aliasName => new \stdClass(),
                ],
            ],
        ]);
    }

    protected function updateIndex($aliasName, $indexClass)
    {
        $this->es->indices()->close(['index' => $aliasName]);

        $this->es->indices()->putSettings([
            'index' => $aliasName,
            'body' => $indexClass::getSettings(),
        ]);

        $this->es->indices()->putMapping([
            'index' => $aliasName,
            'type' => '_doc',
            'body' => [
              '_doc' => [
                  'properties' => $indexClass::getProperties(),
              ],
            ],
        ]);

        $this->es->indices()->open(['index' => $aliasName]);
    }

    protected function reCreateIndex($aliasName, $indexClass)
    {
        $indexInfo = $this->es->indices()->getAliases(['index' => $aliasName]);

        $indexName = array_keys($indexInfo)[0];

        if (!preg_match('~_(\d+)$~', $indexName, $m)) {
          $msg = 'Index name is incorrect:'.$indexName;
          $this->error($msg);
          throw new \Exception($msg);
        }

        $newIndexName = $aliasName.'_'.($m[1] + 1);

        $this->info('Creating index '.$newIndexName);

        $this->es->indices()->create([
            'index' => $newIndexName,
            'body' => [
                'settings' => $indexClass::getSettings(),
                'mappings' => [
                    '_doc' => [
                        'properties' => $indexClass::getProperties(),
                    ],
                ],
            ],
        ]);

        $this->info('Created, going to rebuid data');
        $indexClass::rebuild($newIndexName);

        $this->info('Rebuilt, going to edit alias ');
        $this->es->indices()->putAlias(['index' => $newIndexName, 'name' => $aliasName]);

        $this->info('Edited, goting to delete old alias');
        $this->es->indices()->delete(['index' => $indexName]);
        $this->info('Deleted.');
    }
}
