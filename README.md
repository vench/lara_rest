
#Lp api rest

##install
- Add app.php 

        $app->register(LpRest\RestServiceProvider::class);
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