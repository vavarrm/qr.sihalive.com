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
    }).when("/",
	{
		redirectTo:"/Api/register/register"
    }).when("/admin",
	{
		redirectTo:"/admin"
    }).otherwise(
		{
			controller: 'Error404Controller',
			templateUrl: 'views/Error404.html'
		}
	)
});

var LoginCtrl = function($scope ,$routeParams, apiService,$cookies)
{
	$scope.init = function(){
		
	}
}
pokerInsuranceApp.controller('LoginCtrl',  ['$scope' ,'$routeParams', 'apiService' ,'$cookies', LoginCtrl]);


var pageCtrl = function($scope ,$routeParams, apiService, $cookies, Websokect)
{
	
	$scope.data =
	{
		input :{},
		urlParams:{},
		response :{}
	}
	
	var promise = apiService.Api('/Api/User/getUser');
	promise.then
	(
		// 
		function(r) 
		{
			if(r.data.status =="200")
			{
				
				$scope.data.islogin = r.data.body.islogin;
				var socket = Websokect.open();
				var uid = '001';
				socket.on('connect', function(){
					socket.emit('login', uid);
				});
				if( r.data.body.user_delivery  != null)
				{
					switch(r.data.body.user_delivery.status)
					{
					case "processing":
						$scope.data.step=3;
					  break;
					case 2:
					  break;
					default:
					}
					if()
					{
						
					}					
				}
				// socket.on('update_data',$scope.update_data);
			}
		},
		function() 
		{
			
			var obj ={
				'message' :'system error'
			};
			dialog(obj);
		}
	)
	
	var strUrl = location.href;
	var getPara, ParaVal;
	var aryPara = [];
	if (strUrl.indexOf("?") != -1) 
	{
		var getSearch = strUrl.split("?");
		getPara = getSearch[1].split("&");
		for (var i = 0; i < getPara.length; i++) 
		{
			ParaVal = getPara[i].split("=");
			$scope.data.urlParams[ParaVal[0]] = ParaVal[1];
		}
	}
	
	$scope.calltuktuk = function()
	{
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
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/calltuktuk', $scope.data.urlParams);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.data.response = r.data.body;
					var obj ={
						'message' :r.data.message
					};
					dialog(obj);
					// $scope.step = 4;
					
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
	
	$scope.login =  function()
	{
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
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/login', $scope.data.input);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.data.islogin = r.data.body.islogin;
					var obj ={
						'message' :r.data.message
					};
					dialog(obj);
				}else
				{
					var obj ={
					'message' :r.data.message
					};
					dialog(obj);
				}
				
			},
			function() 
			{
				$scope.ajaxload = false;
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
			}
		)
	}
	
	$scope.init = function(func)
	{
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/'+func, $scope.data.urlParams);
		promise.then
		(
			function(r) 
			{
				
				if(r.data.status =="200")
				{
					$scope.data.response = r.data.body
					if($scope.data.response.isuse =='0')
					{
						// $scope.data.step=3;
					}
					
				}else
				{
					var obj ={
					'message' :r.data.message
					};
					dialog(obj);
				}
				
			},
			function() {
				
				var obj ={
					'message' :'system error'
				};
				dialog(obj);
			}
		)
	}
	
	$scope.starttimer = function()
	{
		$scope.timer = setInterval(function()
		{
			$scope.data.countdown++;
			if($scope.data.countdown > 60)
			{
				$scope.data.countdown = 0;
				$scope.data.resenddisabled =false;
				window.clearInterval($scope.timer);
			}
			$scope.$apply();
		}, 1000);
	}
	
	$scope.resendVerifyCode = function()
	{
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
		
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/resendVerifyCode', $scope.data.input);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.data.resenddisabled = true;
					$scope.data.vid = r.data.body.id;
					$scope.starttimer();
					var obj ={
						'message' :r.data.message
					};
					dialog(obj);
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
	
	$scope.verifyCode = function()
	{
		
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
		
		var promise = apiService.Api('/Api/'+$routeParams.controller+'/verifyCode', $scope.data.input);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.data.step=3;
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
					$scope.data.step=2;
					var user = r.data.body.user;
					$scope.data.input.id = user.id;
					$scope.data.input.vid = user.vid;
					$scope.data.resenddisabled = true;
					$scope.starttimer();
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
pokerInsuranceApp.controller('pageCtrl',  ['$scope' ,'$routeParams', 'apiService','$cookies', 'Websokect' , pageCtrl]);

var bodyCtrl = function($scope ,$routeParams, apiService, $cookies)
{
	
}
pokerInsuranceApp.controller('bodyCtrl',  ['$scope' ,'$routeParams', 'apiService','$cookies', bodyCtrl]);

var Error404Controller = function($scope ,$routeParams, apiService, $cookies)
{
	
}
pokerInsuranceApp.controller('Error404Controller',  ['$scope' ,'$routeParams', 'apiService','$cookies', Error404Controller]);

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


pokerInsuranceApp.factory('Websokect', ['$q', '$rootScope', '$http', function($q, $rootScope, $http) 
{
	return {
		open :function(){
			var socket = {};
			var uid ="001";
			var host =location.protocol + '//' + location.host ;
			socket = io(host+':2120', {secure: true});
			return socket;
		}
    };
}]);