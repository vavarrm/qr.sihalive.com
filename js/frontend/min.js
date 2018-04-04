'use strict';
var pokerInsuranceApp = angular.module("pokerInsuranceApp", ['ngRoute', 'ngCookies']);
pokerInsuranceApp.config(function($routeProvider){
	$routeProvider.when("/",{
		templateUrl: function(params) {
			return 'views/Insurance.html';
		},
		controller: 'InsuranceCount'
    }).otherwise({redirectTo : '/'})
});
var InsuranceCount = function($scope,$routeParams,apiService )
{
	$scope.ajaxload = false;
	$scope.input ={};
	$scope.odds={};
	$scope.step =1;
	$scope.input.famount = 0;
	$scope.input.fpot = 0;
	$scope.init = function()
	{
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'系统忙禄中/system busy',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;
		$scope.input.odds = $scope.odds[$scope.input.outs];		
		var obj = {};
		var promise = apiService.Api('/Api/HdPokerInsurance/getOdds', obj);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.step=1;
					$scope.odds = r.data.body.odds;
					window.scrollTo(0,0);
				
				}else
				{
					var obj ={
					'message' :'system error'
					};
					dialog(obj);
					location.href="/login.html";
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
	
	$scope.$watch('input.pot', function(newValue, oldValue) {
		if(typeof newValue !="undefined")
		{
			$scope.input.i_maximum =Math.floor(newValue/$scope.odds[$scope.input.outs]*10)/10;
			$scope.input.percentage50 =  Math.floor(newValue/2/$scope.odds[$scope.input.outs]*10)/10;
			$scope.input.amount = $scope.input.i_maximum;
		}
		
	});
	
	$scope.$watch('input.result', function(newValue, oldValue) {
		if(typeof newValue !="undefined")
		{
			if(newValue =="pay")
			{
				$scope.input.payamount =$scope.input.insuredamount-$scope.input.famount;
				$scope.input.payamount_disable = false;
			}else{
				$scope.input.payamount = 0;
				$scope.input.payamount_disable = true;
			}
		}
		
	});
	
	$scope.$watch('input.payamount', function(newValue, oldValue) {
		if(typeof newValue !="undefined")
		{
			if(newValue > $scope.input.pot)
			{
				$scope.input.payamount = oldValue;
				return false;
			}
		}
	});
	// $scope.step=2;
	
	$scope.check_user_code = function()
	{
		if(typeof $scope.input.ucode.length=="undefined" || $scope.input.ucode.length != 6)
		{
			return false;
		}
		
		if(typeof $scope.input.ucode =="undefined" || $scope.input.ucode =="")
		{
			return false;
		}
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'系统忙禄中/system busy',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;		
		var obj = $scope.input;
		var promise = apiService.Api('/Api/HdPokerInsurance/chenkUserCode', obj);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.checkvcode =r.data.body.check;
				
				}else
				{
					var obj =
					{
						'message' :r.data.message,
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
	
	$scope.$watch('input.outs', function(newValue, oldValue)
	{
	
		
		if(newValue >20)
		{
			$scope.input.outs = oldValue;
			return false;
		}
		
		
		if(typeof newValue !="undefined" && typeof $scope.input.pot !="undefined")
		{
			
			$scope.input.i_maximum =  Math.floor($scope.input.pot/$scope.odds[$scope.input.outs]*10)/10;
			$scope.input.percentage50 =  Math.floor($scope.input.pot/2/$scope.odds[$scope.input.outs]*10)/10;
			$scope.input.amount = $scope.input.i_maximum;
		}
	});
	
	$scope.$watch('input.amount', function(newValue, oldValue)
	{
		if(typeof newValue !="undefined" && newValue !=null)
		{
			if(newValue >$scope.input.i_maximum)
			{
				$scope.input.amount = oldValue;	
				var obj =
				{
					'message' :'超出购卖额度上限/over maximum',
				};
				dialog(obj);
			}else
			{
				$scope.input.insuredamount=Math.floor(newValue*$scope.odds[$scope.input.outs]*10)/10;
				if(isNaN($scope.input.insuredamount))
				{
					$scope.input.insuredamount =0;
				}
			}
			
			if($scope.input.result == 'pay' && newValue>0)
			{
				$scope.input.payamount =$scope.input.insuredamount-$scope.input.famount;
			}
		}

	});
	
	$scope.goTop = function()
	{
		window.scrollTo(0,0);
	}
	
	$scope.newGame = function()
	{
		$scope.step =1;
		$scope.input={};
	}
	
	$scope.update_result = function()
	{
		
	}
	
	$scope.end =function(){
		$scope.save();
		return false;
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'系统忙禄中/system busy',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;
		$scope.input.odds = $scope.odds[$scope.input.outs];		
		var obj = $scope.input;
		var promise = apiService.Api('/Api/HdPokerInsurance/uploadResult', obj);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					var obj =
					{
						'message' :'已储存/next Game',
					};
					dialog(obj);
					$scope.input.confirm =true;
					
				}else
				{
					var obj =
					{
						'message' :r.data.message,
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
	
	$scope.buyTurn=function()
	{
		
		if($scope.input.round =="flop")
		{
			$scope.input.round = "turn";
			if($scope.input.result !="pay")
			{
				// $scope.input.turnAutoBuy =true;
				// $scope.input.fpot = $scope.input.pot;
				// $scope.input.famount = $scope.input.amount;
				// $scope.input.pot = $scope.input.pot-$scope.input.amount;
				// $scope.input.nowbuyturn = true;
			}
			// $scope.input.famount = $scope.input.amount ;
			$scope.input.round_disabled = true;
			$scope.input.players_disabled = true;
			$scope.input.pot_disabled = true;
		}
		$scope.step =1;
		$scope.input.result="";
	}
	
	$scope.save = function()
	{	
		$scope.input.confirm = false;
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'系统忙禄中/system busy',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;
		$scope.input.odds = $scope.odds[$scope.input.outs];		
		var obj = $scope.input;
		var promise = apiService.Api('/Api/HdPokerInsurance/insert', obj);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.input.order_id =r.data.body.order_id;
					$scope.input.order_number =r.data.body.order_number;
					var obj =
					{
						'message' :'已储存/next Game',
					};
					dialog(obj);
					$scope.input.confirm =true;
				}else
				{
					var obj =
					{
						'message' :r.data.message,
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
pokerInsuranceApp.controller('InsuranceCount',  ['$scope' ,'$routeParams', 'apiService', InsuranceCount]);

var LoginCtrl = function($scope ,$routeParams, apiService,$cookies)
{

	$scope.login = function (){
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'系统忙禄中/system busy',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;		
		var obj = $scope.input;
		var promise = apiService.Api('/Api/Api/login', obj);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$cookies.put('usess', r.data.body.user_sess, { path: '/'});
					location.href="/";
				}else
				{
					var obj =
					{
						'message' :r.data.message,
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
pokerInsuranceApp.controller('LoginCtrl',  ['$scope' ,'$routeParams', 'apiService' ,'$cookies', LoginCtrl]);



var bodyCtrl = function($scope ,$routeParams, apiService, $cookies)
{
	$scope.user={};
	$scope.logout = function()
	{
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'系统忙禄中/system busy',
			};
			dialog(obj);
			return false;
		}
		var obj =
		{
			'message'  :'确认登出/confirm logout',
			buttons: 
			[
				{
					text: "yes",
					click: function() 
					{
						$scope.ajaxload = true;		
						var obj = {};
						var promise = apiService.Api('/Api/Api/logout', obj);
						promise.then
						(
							function(r) 
							{
								$scope.ajaxload = false;
								if(r.data.status =="200")
								{
									$cookies.remove("usess");
									location.href="/login.html"
								}else
								{
									
									var obj =
									{
										'message' :r.data.message,
										buttons: 
										[
											{
												text: "close",
												click: function() 
												{
													$( this ).dialog( "close" );
													location.href="/login.html"
												}
											}
										]
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
				},
				{
					text: "no",
					click: function() 
					{
						$( this ).dialog( "close" );
					}
				},
			]
		};
		dialog(obj);
	}
	$scope.init = function()
	{
		
		if($scope.ajaxload == true)
		{
			var obj =
			{
				'message' :'系统忙禄中/system busy',
			};
			dialog(obj);
			return false;
		}
		$scope.ajaxload = true;		
		var obj = {};
		var promise = apiService.Api('/Api/Api/getUser', obj);
		promise.then
		(
			function(r) 
			{
				$scope.ajaxload = false;
				if(r.data.status =="200")
				{
					$scope.user = r.data.body.user_data;
					
				}else
				{
					location.href='/login.html';
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