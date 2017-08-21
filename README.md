
#Lp api rest

##install
- Add app.php $app->register(LpRest\RestServiceProvider::class);
- Add in AppServiceProvider::register
        
        app()->afterResolving(CommonRepositoryModelProvider::class, 
                function(CommonRepositoryModelProvider $mp) {              
              $mp->addModelAliases('user', \App\User::class);
          });