
#Lp api rest

This package allows you to create a simple REST API on the basis of standard models (Eloquent) of the Larave 5.x Framework. 

##install
- composer require venya/lp-rest

##Configure
- Add app.php 

        $app->register(LpRest\RestServiceProvider::class);
        
        //$app->register(LpRest\RestServiceProviderLumen::class); //for Lumen
        
- Add in AppServiceProvider::register
        
        //Set model aliases
        app()->afterResolving(CommonRepositoryModelProvider::class, 
                function(CommonRepositoryModelProvider $mp) {              
              $mp->addModelAliases('user', \App\User::class);
          });               
        
        //Change access provider
        $this->app->bind(CommonRepositoryAccessProvider::class, ApiAccessProvider::class );   
           
        //Change response schema   
        $this->app->bind(CommonResponse::class, ApiCustomResponse::class ); 
          
        //Custom routes and etc  
        $this->app->afterResolving(RestServiceHelper::class,
                    function(RestServiceHelper $sp) {
                        $sp->setRouteGroupOptions([
                            'prefix'         => 'api/rest',
                            //'middleware'     => 'auth',
                        ]);
                    });  