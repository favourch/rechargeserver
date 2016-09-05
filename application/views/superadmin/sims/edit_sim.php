<script>
    function number_validation(phoneNumber) {
        var regexp = /^((^\880|0)[1][1|5|6|7|8|9])[0-9]{8}$/;
        var validPhoneNumber = phoneNumber.match(regexp);
        if (validPhoneNumber) {
            return true;
        }
        return false;
    }
    function edit_sim(simInfo, serviceList) {
        if (typeof simInfo.simNo == "undefined" || simInfo.simNo.length == 0) {
            $("#content").html("Please give sim naumber !");
            $('#common_modal').modal('show');
            return;
        }
        if (number_validation(simInfo.simNo) == false) {
            $("#content").html("Please give a valid SIM Number");
            $('#common_modal').modal('show');
            return;
        }
        if (typeof simInfo.identifier == "undefined" || simInfo.identifier.length == 0) {
            $("#content").html("Please give an identifier !");
            $('#common_modal').modal('show');
            return;
        }
        for (var i = 0; i < serviceList.length; i++) {
            var serviceInfo = serviceList[i];
            if (serviceInfo.selected == true) {
                if (typeof serviceInfo.currentBalance == "undefined" || serviceInfo.currentBalance == 0) {
                    $("#content").html("Please give an amount for " + serviceInfo.title);
                    $('#common_modal').modal('show');
                    return;
                }
            }
        }
        angular.element($("#submit_edit_sim_btn")).scope().editSim(simInfo, function (data) {
            $("#content").html(data.message);
            $('#common_modal').modal('show');
            $('#modal_ok_click_id').on("click", function () {
                window.location = '<?php echo base_url() ?>superadmin/sim/edit_sim/' + simInfo.simNo;
            });
        });

    }
</script>


<div class="panel-heading">Edit Sim</div>
<div class="panel-body" ng-controller="simController">
    <div class="form-background top-bottom-padding">
        <div class="row">
            <div class ="col-md-4 margin-top-bottom">
                <form>
                    <?php if (isset($sim_info)) { ?>
                        <div ng-init="setSimInfo(<?php echo htmlspecialchars(json_encode($sim_info)) ?>)"></div>
                    <?php } ?>
                    <div class="row form-group">
                        <label for="sim_number" class="col-md-6 control-label requiredField">
                            Sim Number:
                        </label>
                        <div class ="col-md-6">
                            <input type="text" placeholder="88017XXXXXXXX" value="" class="form-control" placeholder=""  id="" ng-model="simInfo.simNo">
                        </div> 
                    </div>
                    <div class="row form-group">
                        <label for="identifier" class="col-md-6 control-label requiredField">
                            Identifier:
                        </label>
                        <div class ="col-md-6">
                            <input type="text" placeholder="localserver1" value="" class="form-control" placeholder=""  id="" ng-model="simInfo.identifier">
                        </div> 
                    </div>
                    <div class="row form-group">
                        <label for="description" class="col-md-6 control-label requiredField">
                            Description:
                        </label>
                        <div class ="col-md-6">
                            <input type="text" value="" class="form-control" placeholder=""  id="" ng-model="simInfo.description">
                        </div> 
                    </div>
                    <div class="row form-group"  ng-init="setSimStatusList('<?php echo htmlspecialchars(json_encode($sim_status_list)); ?>')">
                        <label for="status" class="col-md-6 control-label requiredField">
                            Status:
                        </label>
                        <div class ="col-md-6">
                            <select  class="form-control" ng-model='simInfo.status' required ng-options='statusInfo.id as statusInfo.title for statusInfo in simStatusList'></select>
                        </div> 
                    </div>
                </form>
            </div>
            <div class="col-md-8" ng-init="setSimCategoryList('<?php echo htmlspecialchars(json_encode($sim_category_list)); ?>')">
                <div class="row col-md-12">
                    <label for="sim_member" class="control-label requiredField">
                        Services
                    </label>
                </div>
                <div class="row">
                    <label for=""  class="col-md-4 control-label requiredField">
                        <input type="checkbox" ng-model="selectedAll" ng-click="checkAll()" />
                        Select All
                    </label>
                    <label for=""  class="col-md-4 control-label requiredField">
                        category type
                    </label>
                    <label for=""  class="col-md-4 control-label requiredField">
                        current balance
                    </label>
                </div>
                <div class=" row"  ng-init="setServiceList(<?php echo htmlspecialchars(json_encode($service_list)); ?>)" >
                    <div ng-repeat="serviceInfo in serviceList">
                        <div class="col-md-4">
                            <input ng-model="serviceInfo.selected" type="checkbox" value="{{serviceInfo.service_id}}" name="per[]"  ng-click="toggleSelection(serviceInfo)">{{serviceInfo.title}}
                        </div>
                        <div class="col-md-4">
                            <select  ng-model='serviceInfo.categoryId' required ng-options='category.id as category.title for category in simCategoryList'></select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" value="" placeholder="1000"  id="" ng-model="serviceInfo.currentBalance">
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="row form-group">
        <div class ="col-md-3 pull-right">
            <input id="submit_edit_sim_btn" name="submit_edit_sim_btn" class="btn btn_custom_button" type="submit" onclick="edit_sim(angular.element(this).scope().simInfo, angular.element(this).scope().serviceList)" value="Update"/>
        </div> 
    </div>
</div>

