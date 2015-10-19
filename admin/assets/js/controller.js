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
    $scope.storeToken = function(){
      $http.post(
                $window.ajaxUrl, {
                    action: 'customImageImporterSaveToken',
                    token: $scope.tokenModel
                }).then(function(response) {
                        $scope.isTokenStored = true;
                        $scope.isLoading = true;
                        $scope.getItems();
                    },function(){
                        alert('Error occured');
                    }
                )
    };
    $scope.saveToken = function(){
      $http.get(('http://zoommyapp.com/api/v1/integration/check.json?token='+$scope.tokenModel)).
        then(function(response) {
            $scope.storeToken();
          }, function(){
            alert('Token invalid');
          });
    };
    $scope.getItems = function(){
      $scope.itemsList = [];
      $http({
          method: 'GET',
          url: ('http://zoommyapp.com/api/v1/integration/favorites.json?token='+$scope.tokenModel)
      }).success(function(data) {
          $scope.itemsList = data.integration; ///data.items;
          $scope.isLoading = false;
      }).error(function(data){
          $scope.isLoading = false;
          $scope.itemsList = [];
      });
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
        $scope.tokenModel = $window.zoommy_token;
        if($scope.tokenModel == ""){
          $scope.isTokenStored = false;
        }else{
          $scope.isTokenStored = true;
        }
        $scope.isLoading = true;
        $scope.getItems();
    }
}]);
