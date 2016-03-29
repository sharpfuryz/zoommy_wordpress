angular.module('zoommyApp', ['akoenig.deckgrid'])
  .config(function ($httpProvider) {
    $httpProvider.defaults.transformRequest = function(data){
        if (data === undefined) {
            return data;
        }
        return jQuery.param(data);
    };
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
  })
.controller('imagesController',['$scope','$http','$window',function($scope,$http,$window){
    $scope.storeToken = function(val){
      $http.post(
                $window.ajaxUrl, {
                    action: 'customImageImporterSaveToken',
                    token: val
                }).then(function(response) {
                        $scope.isTokenStored = true;
                        $scope.isLoading = true;
                        $scope.getItems();
                        $scope.getCollections();
                    },function(){
                        alert('Error occured');
                    }
                )
    };
    $scope.loginToAccount = function(){
      $http({method: 'POST',url: '//zoommyapp.com/api/v1/users/sign_in', params: {email: $scope.accountEmail, password: $scope.accountPassword, platform: 'wordpress_plugin', hardware_id: Math.random()*100000}}).then(function(response) {
            $scope.storeToken(response.data.integration_token);
          }, function(){
            alert('Token invalid');
          });
    }
    $scope.getItems = function(){
      $scope.itemsList = [];
      $scope.selectedCollectionId = 0;
      $http({
          method: 'GET',
          url: ('//zoommyapp.com/api/v2/favorites.json?integration_token='+$scope.tokenModel)
      }).success(function(data) {
          $scope.itemsList = data.items;
          $scope.isLoading = false;
      }).error(function(data){
          $scope.isLoading = false;
          $scope.itemsList = [];
      });
    };
    $scope.selectCollection = function(collection){
      $scope.selectedMode = 'collection';
      $scope.isLoading = true;
      $scope.selectedCollectionId = collection.id;
      $http({method: 'GET', url: '//zoommyapp.com/api/v2/collections/'+collection.id+'.json', params: {integration_token: $scope.tokenModel}}).success(function(data){
        $scope.isLoading = false;
        $scope.itemsList = data.items;
      });
    };
    $scope.getCollections = function(){
      $http({method: 'GET', url: '//zoommyapp.com/api/v2/endpoint/user.json?integration_token='+$scope.tokenModel}).success(function(data){
        $scope.collectionsList = data.collections;
        $scope.favoritesSize   = data.favorites_count;
        $scope.selectedMode = 'favorites';
      })
    };
    $scope.selectItem = function(card){
        $scope.selectedItem = card;
        card.loading = true;
        $http.post(
            $window.ajaxUrl,
            {
                action: 'customImageImporterSaveImage',
                imageUrl: card.full_url
            }
          ).then(function(){
            card.loading = false;
          }, function(){
            card.loading = false;
          });
    };
    $scope.init = function(){
        $scope.itemsList = [];
        $scope.accountEmail = "";
        $scope.accountPassword = "";
        $scope.tokenModel = $window.zoommy_token;
        if($scope.tokenModel == ""){
          $scope.isTokenStored = false;
        }else{
          $scope.isTokenStored = true;
          $scope.getItems();
          $scope.getCollections();
        }
        $scope.isLoading = true;
    }
}]);
