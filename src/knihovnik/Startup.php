<?php
use routing\QueryParametersRouter;
use middlewares\MVCMiddleware;
use middlewares\MiddlewarePipeline;
use middlewares\AuthorizationMiddleware;
use dependencyInjection\DependencyProviderInterface;
use dependencyInjection\DependencyConfigurationInterface;

class Startup {
    public function configureDependencies(DependencyConfigurationInterface $config) {
        $config->for("\PDO")->useInstance($this->createPDOConnection());

        $config->for("\domain\book\BookRepositoryInterface")->useClass("\domain\book\PDOBookRepository");
        $config->for("\domain\author\AuthorRepositoryInterface")->useClass("\domain\author\PDOAuthorRepository");
        $config->for("\domain\place\PlaceRepositoryInterface")->useClass("\domain\place\PDOPlaceRepository");
        $config->for("\domain\condition\ConditionRepositoryInterface")->useClass("\domain\condition\PDOConditionRepository");
        $config->for("\domain\genre\GenreRepositoryInterface")->useClass("\domain\genre\PDOGenreRepository");
        $config->for("\domain\user\UserRepositoryInterface")->useClass("\domain\user\PDOUserRepository");
        $config->for("\domain\bookRequest\BookRequestRepositoryInterface")->useClass("\domain\bookRequest\PDOBookRequestRepository");
    }
    
    public function configurePipeline(MiddlewarePipeline $pipeline, DependencyProviderInterface $provider) {
        $router = new QueryParametersRouter(BASE_URL."/knihovnik/", [
            "controller" => "Home",
            "action" => "index"
        ]);
        $authorization = new AuthorizationMiddleware($provider, $provider->provide("\domain\user\UserRepositoryInterface"));
        $pipeline->add($authorization);
        $mvc = new MVCMiddleware($router, $provider);
        $pipeline->add($mvc);
    }
    
    private function createPDOConnection(): \PDO {
        $hostname = DB_HOST;
        $databaseName = DB_NAME;
        $username = DB_USER;
        $password = DB_PASS;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ];
        
        $PDO = new PDO("mysql:host=$hostname;dbname=$databaseName", $username, $password, $options);

        return @$PDO;
    }
}