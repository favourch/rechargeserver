<script type="text/javascript">
    $(function () {
        $('#start_date').Zebra_DatePicker();
        $('#start_date').val('<?php echo $current_date ?>');
        $('#end_date').Zebra_DatePicker();
        $('#end_date').val('<?php echo $current_date ?>');
        $('#repeatSelect').val('<?php echo TRANSACTION_STATUS_ID_SUCCESSFUL ?>');
    });
    function search_receive_history(searchInfo) {
        var startDate = $("#start_date").val();
        var endDate = $("#end_date").val();
        angular.element($("#search_submit_btn")).scope().getAllHistory(startDate, endDate);
    }
</script>

<div class="loader"></div>
<div class="ezttle"><span class="text"> Transaction History</span></div>
<div class="mypage" ng-controller="transctionController">
    <ul class="list-unstyled paymentHistorySearch" ng-init="setTransactionStatusList('<?php echo htmlspecialchars(json_encode($transction_status_list)) ?>')">
        <li>Start Date</li>
        <li><input id="start_date" type="text" size="18" placeholder="Start Date"  name="from" class="form-control input-xs customInputMargin"></li>
        <li>End Date</li>
        <li><input id="end_date" type="text" size="18" placeholder="End Date"  name="from" class="form-control input-xs customInputMargin"></li>
        <li>Cell No</li>
        <li> <input type="text" class="form-control input-xs customInputMargin" placeholder="01XXXXXXXXX" ng-model="searchInfo.cellNo"></li>
        <li>Status Type</li>
        <li>
            <select  ng-model='searchInfo.statusId' required ng-options='transactionStatus.id as transactionStatus.title for transactionStatus in transactionStatusList' class="form-control input-xs"></select>
        </li>
        <li>Show All</li>
        <li> <input type="checkbox" ng-model="allTransactions"></li>
        <li><input id="search_submit_btn" type="submit" size="18" value="Search" onclick="search_receive_history(angular.element(this).scope().searchInfo)" class="button-custom"></li>
    </ul>
    <table class="table10" ng-init="setTransactionInfoList(<?php echo htmlspecialchars(json_encode($transaction_list)) ?>, <?php echo htmlspecialchars(json_encode($total_transactions)) ?>, <?php echo htmlspecialchars(json_encode($total_amount)) ?>)">
        <thead>
            <tr>
                <th><a href="">Id</a></th>
                <th><a href="">Username</a></th>
                <th><a href="">Service</a></th>
                <th><a href="">Sender</a></th>
                <th><a href="">Number</a></th>
                <th><a href="">Amount</a></th>
                <th><a href="">Status</a></th>
                <th><a href="">Transaction Id</a></th>
                <th><a href="">Date</a></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
        <li style="display: none" dir-paginate="paymentInfo in transctionInfoList|itemsPerPage:pageSize" current-page="currentPage"></li>
        <tr ng-repeat="transctionInfo in transctionInfoList">
            <th>{{transctionInfo.transaction_id}}</th>
            <th>{{transctionInfo.username}}</th>
            <th>{{transctionInfo.service_title}}</th>
            <th>{{transctionInfo.sender_cell_no}}</th>
            <th>{{transctionInfo.cell_no}}</th>
            <th>{{transctionInfo.amount}}</th>
            <th>{{transctionInfo.status}}</th>
            <th>{{transctionInfo.trx_id_operator}}</th>
            <th>{{transctionInfo.created_on}}</th>
        </tr>
        </tfoot>
    </table>
    <div class="form-group">
        <div class="col-md-12 fleft">
            <div class="summery">
                <p>Summary</p>
                <table>
                    <tbody>
                        <tr><td>Current Page Amount :</td><td class="amt">{{currentPageAmount}}</td></tr>
                        <tr><td>Total Amount :</td><td class="amt">{{totalAmount}}</td></tr>
                    </tbody>
                </table>
            </div>
        </div> 
    </div>

    <div class="other-controller">
        <div class="text-center">
            <dir-pagination-controls boundary-links="true" on-page-change="getTransctionByPagination(newPageNumber)" template-url="<?php echo base_url(); ?>history/pagination_tmpl_load"></dir-pagination-controls>
        </div>
    </div>
</div>
<div class="row"></div>
<div class="row"></div>
<div class="row"></div>
