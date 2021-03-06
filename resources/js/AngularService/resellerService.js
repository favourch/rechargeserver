angular.module('services.Reseller', []).
        factory('resellerService', function ($http, $location) {
            var $app_name = "/rechargeserver";
            //var $app_name = "";
            var resellerService = {};
            resellerService.getUserServiceList = function () {

                return $http({
                    method: 'post',
                    url: $location.path() + $app_name + '/reseller/get_user_service_list',
                    data: {
                    }
                });
            }
            resellerService.createReseller = function (resellerInfo) {

                return $http({
                    method: 'post',
                    url: $location.path() + $app_name + '/reseller/create_reseller',
                    data: {
                        resellerInfo: resellerInfo
                    }
                });
            }
            resellerService.updateReseller = function (resellerInfo) {

                return $http({
                    method: 'post',
                    url: $location.path() + $app_name + '/reseller/update_reseller/'+resellerInfo.user_id,
                    data: {
                        resellerInfo: resellerInfo
                    }
                });
            }
            resellerService.updateUserProfile = function (resellerInfo) {

                return $http({
                    method: 'post',
                    url: $location.path() + $app_name + '/reseller/update_user_profile/'+resellerInfo.user_id,
                    data: {
                        resellerInfo: resellerInfo
                    }
                });
            }
            resellerService.updateServiceRate = function (userId, updateRate) {

                return $http({
                    method: 'post',
                    url: $location.path() + $app_name + '/reseller/update_rate/' +userId,
                    data: {
                        updateRate: updateRate
                    }
                });
            }

            return resellerService;
        });

