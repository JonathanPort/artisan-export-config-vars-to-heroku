<?php

namespace JonathanPort\ArtisanCommands\Console;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Console\Command;

class ExportConfigVarsToHeroku extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export-config-vars-to-heroku {--include-empty-values}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports selected .env file values to Heroku app.';


    /**
     * Perform the export
     */
    public function handle()
    {

        // Check heroku app is connected
        if (! $this->checkHerokuAppIsConnected()) return $this->error('No Heroku remote found.');

        // Ask user what file to read
        $configFile = $this->ask('What config file would you like to read from?');

        // Grab the file. PHP will throw error if file not found.
        $file = file($configFile);

        // Grab option
        $includeEmptyValues = $this->option('include-empty-values');

        // Loop over the files lines
        foreach ($file as $line) {

            // Trim the line for new lines and white spaces
            $line = trim($line);

            // If blank line, skip.
            if (! $line) continue;

            // Format and split line into array
            $line = $this->splitLineIntoParts($line);

            // Set config vars
            $key = $line[0];
            $value = $line[1];

            // If no value, skip.
            if (! $includeEmptyValues && ! $value) {
                $this->info("Skipping {$key} as it contains no value.");
                continue;
            }

            // Set progress bar message
            $this->info("Setting {$key} with value: {$value}");

            // Run heroku config:set command
            $this->runHerokuSetVarCommand($key, $value);

        }

        // Finish
        return $this->info('Config vars exported to Heroku successfully!');

    }


    /**
     * Splits the line into the key and value
     */
    private function splitLineIntoParts($line)
    {

        $line = str_replace(' ', '', $line);
        $line = str_replace("'", '"', $line);

        $line = explode('=', $line, 2);

        $line[0] = trim($line[0]);
        $line[1] = trim($line[1]);

        return $line;

    }


    /**
     * Runs the new heroku command using Symthony's Process module.
     */
    private function runHerokuSetVarCommand($key, $value) {

        $process = new Process("heroku config:set {$key}={$value}");
        $process->run();

    }


    /**
     * Checks of heroku app is found using 'heroku info' command.
     * If 'No app specified.' is found in output, returns false;
     */
    private function checkHerokuAppIsConnected()
    {

        $process = new Process('heroku info');
        $process->run();

        return strpos($process->getErrorOutput(), 'No app specified.') ? false : true;

    }


}