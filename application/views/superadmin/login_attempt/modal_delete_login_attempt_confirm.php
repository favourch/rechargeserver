<script type="text/javascript">
    $(function() {
        $("#button_delete").on("click", function() {
            $.ajax({
                dataType: 'json',
                type: "POST",
                url: '<?php echo base_url(); ?>' + "superadmin/login_attempt/delete_login_attempt",
                data: {
                    delete_id: $("#input_delete_id").val()
                },
                success: function(data) {
                    $("#modal_delete_confirm").modal('hide');
                    $("#content").html(data.message);
                    $('#common_modal').modal('show');
                    $('#modal_ok_click_id').on("click", function() {
                        window.location = '<?php echo base_url() ?>superadmin/login_attempt';
                    });
                    return;
                }
            });
        });
    });
    function open_modal_delete_confirm(delete_id) {
        $('#input_delete_id').val(delete_id);
        $("#modal_delete_confirm").modal('show');
    }
</script>
<div class="modal fade" id="modal_delete_confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Login Attempt</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="row form-group">
                        <label class="col-sm-offset-2 col-sm-10 control-label">Are you sure to delete this login attempt?</label>
                        <input id="input_delete_id" name="input_delete_id" value="" type="hidden" class="form-control"/>
                    </div>
                </div>                
            </div>
            <div class="modal-footer">
                <div class ="col-md-offset-6 col-md-3">
                    <button style="width:100%" id="button_delete" name="button_delete" value="" class="form-control btn button-custom pull-right">Delete</button>
                </div>
                <div class ="col-md-3">
                    <button style="width:100%" type="button" class="btn button-custom" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>