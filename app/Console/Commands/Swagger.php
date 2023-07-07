<?php
use Symfony\Component\Console\Command\Command;

class Swagger extends Command {
    protected  $signature = 'swagger';

    protected $description = 'este comando gera a documentação corrente do swagger API';

    public function handle(){
        $openapi = \OpenApi\Generator::scan([config('swagger.sources')]);
        file_put_contents('public/docs/swagger.json', $openapi->toJson());
        $this->info('Api doc gerado com sucesso!');
        return Command::SUCCESS;
    }
}