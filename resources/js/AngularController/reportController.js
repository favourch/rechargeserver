angular.module('controller.Report', ['services.Report']).
        controller('reportController', function ($scope, reportService) {
            $scope.currentPage = 1;
            $scope.pageSize = 10;
            $scope.profitList = [];
            $scope.reportList = [];
            $scope.userProfits = [];
            $scope.serviceList = [];
            $scope.reportSummery = {};
            $scope.transactionStatusList = [];
            $scope.userTotalProfitInfo = {};
            $scope.searchInfo = {};
            $scope.allTransactions = false;
            $scope.setUserProfits = function (userProfits) {
                $scope.userProfits = JSON.parse(userProfits);
                $scope.userTotalProfitInfo.usertotalUsedAmount = 0;
                $scope.userTotalProfitInfo.userTotalProfit = 0;
                angular.forEach($scope.userProfits, function (profit) {
                    $scope.userTotalProfitInfo.usertotalUsedAmount = $scope.userTotalProfitInfo.usertotalUsedAmount + +profit.total_used_amount;
                    $scope.userTotalProfitInfo.userTotalProfit = $scope.userTotalProfitInfo.userTotalProfit + +profit.total_profit;

                });
            }

            $scope.setProfitList = function (profitList, collectionCounter) {
                $scope.profitList = JSON.parse(profitList);
                setCollectionLength(collectionCounter);
            }
            $scope.setTransactionStatusList = function (transactionStatusList) {
                $scope.transactionStatusList = JSON.parse(transactionStatusList);
                $scope.searchInfo.statusId = 0;
            }
            $scope.setServiceIdList = function (serviceList) {
                $scope.serviceList = JSON.parse(serviceList);
                $scope.searchInfo.serviceId = 0;
            }
            $scope.setReportList = function (reportList, reportSummery) {
                $scope.reportList = JSON.parse(reportList);
                $scope.reportSummery = JSON.parse(reportSummery);
            }


            $scope.getRepotHistory = function (startDate, endDate) {

                $scope.searchInfo.limit = $scope.allTransactions;
                if (startDate != "" && endDate != "") {
                    $scope.searchInfo.fromDate = startDate;
                    $scope.searchInfo.toDate = endDate;
                }
                reportService.getRepotHistory($scope.searchInfo).
                        success(function (data, status, headers, config) {
                            $scope.profitList = data.profit_list;
                            if ($scope.allTransactions != false) {
                                $scope.pageSize = data.total_transactions;
                                $scope.allTransactions = false;
                            }
                            setCollectionLength(data.total_transactions);
                        });
            };
            $scope.getDetailRepotHistory = function (startDate, endDate) {

                $scope.searchInfo.limit = $scope.allTransactions;
                if (startDate != "" && endDate != "") {
                    $scope.searchInfo.fromDate = startDate;
                    $scope.searchInfo.toDate = endDate;
                }
                reportService.getDetailRepotHistory($scope.searchInfo).
                        success(function (data, status, headers, config) {
                            $scope.reportList = data.report_list;
                            $scope.reportSummery = data.report_summary;
                            if ($scope.allTransactions != false) {
                                $scope.allTransactions = false;
                            }
                        });
            };
            $scope.getProfitLossHistory = function (startDate, endDate) {

                $scope.searchInfo.limit = $scope.allTransactions;
                if (startDate != "" && endDate != "") {
                    $scope.searchInfo.fromDate = startDate;
                    $scope.searchInfo.toDate = endDate;
                }
                reportService.getProfitLossHistory($scope.searchInfo).
                        success(function (data, status, headers, config) {
                            $scope.reportList = data.report_list;
                            $scope.reportSummery = data.report_summary;
                            if ($scope.allTransactions != false) {
                                $scope.allTransactions = false;
                            }
                        });
            };
            $scope.getProfitByPagination = function (num) {
                $scope.searchInfo.offset = getOffset(num);
                reportService.getBkashTransactionList($scope.searchInfo).
                        success(function (data, status, headers, config) {
                            $scope.profitList = data.profit_list;
                        });
            };
            function getOffset(number) {
                var initIndex;
                initIndex = $scope.pageSize * (number - 1);
                return initIndex;
            }

        });


