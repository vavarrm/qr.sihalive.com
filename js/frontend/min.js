'use strict';
var pokerInsuranceApp = angular.module("pokerInsuranceApp", ['ngRoute', 'ngCookies']);
pokerInsuranceApp.config(function($routeProvider)
{
	$routeProvider.when("/:controller/:func/:page",
	{
		templateUrl: function(params) 
		{
			return 'views/'+params.page+'.html';
		},
		controller: 'pageCtrl'
    }).otherwise({redirectTo : '/Api/register/register'})
});

var LoginCtrl = function($scope ,$routeParams, apiService,$cookies)
{
	$scope.init = function(){
		
	}
}
pokerInsuranceApp.controller('LoginCtrl',  ['$scope' ,'$routeParams', 'apiService' ,'$cookies', LoginCtrl]);


var pageCtrl = function($scope ,$routeParams, apiService, $cookies)
{
	$scope.data =
	{
		input :{}
	}
	
	$scope.singup = function()
	{
		if($scope.data.input.password != $scope.data.input.c_password)
		{
			var obj =
			{
				'message' :'系统忙禄中/system busy',
			};
			dialog(obj);
			return false;
		}
		
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'loading.....',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;	
		var obj = $scope.data.input;
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/'+$routeParams.func, obj);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					
				
				}else
				{
					var obj ={
					'message' :r.data.message
					};
					dialog(obj);
				}
				
			},
			function() {
				$scope.ajaxload = false;
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
			}
		)
		
	}
}
pokerInsuranceApp.controller('pageCtrl',  ['$scope' ,'$routeParams', 'apiService','$cookies', pageCtrl]);

var bodyCtrl = function($scope ,$routeParams, apiService, $cookies)
{
	
}
pokerInsuranceApp.controller('bodyCtrl',  ['$scope' ,'$routeParams', 'apiService','$cookies', bodyCtrl]);

var apiService = function($http,$cookies)
{
	var sess = $cookies.get('usess');
	return {
		Api :function(apiurl, obj)
		{
			var default_obj = 
			{
			
			};
			var object = $.extend(default_obj, obj);
			return $http.post(apiurl+'?sess='+sess , object,  {headers: {'Content-Type': 'application/json'}});
		}
    };
}
pokerInsuranceApp.factory('apiService', ['$http','$cookies', apiService]);