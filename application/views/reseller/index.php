<div class="ezttle"><span class="text"><?php echo $title;?></span></div>
<div class="mypage">
    <?php if($group !== TYPE4){?>
    <div class="btn-group">
        <a href="<?php echo base_url().'reseller/create_reseller'?>" class="btn btn-primary btn-sm" href="reSellersAdd.html"><span class="glyphicon glyphicon-plus-sign"></span> Add Reseller</a>
    </div>
    <?php } ?>
    <div class="top10">&nbsp;</div>
        <input type="hidden" style="display:none;" value="" name="elctkn">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th><a href="">Username</a></th>
                    <th><a href="">Name</a></th>
                    <th><a href="">Email</a></th>
                    <th><a href="">Mobile</a></th>
                    <th><a href="">Balance</a></th>
                    <th><a href="">Created Date</a></th>
                    <th><a href="">Last Login</a></th>
                    <th width="170">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reseller_list as $reseller_info){?>
                <tr>
                    <td><a href="<?php echo base_url().'reseller/update_reseller/'.$reseller_info['user_id']?>"><?php echo $reseller_info['username']?></a></td>
                    <td><?php echo $reseller_info['first_name'].' '.$reseller_info['last_name']?></td>
                    <td><?php echo $reseller_info['email']?></td>
                    <td><?php echo $reseller_info['mobile']?></td>
                    <td><?php echo $reseller_info['current_balance']?></td>
                    <td><?php echo $reseller_info['created_on']?></td>
                    <td><?php echo $reseller_info['last_login']?></td>
                    <td class="action">
                        <a href="<?php echo base_url().'payment/create_payment/'.$reseller_info['user_id'];?>">Payment</a> | 
                        <a href="<?php echo base_url().'reseller/update_rate/'.$reseller_info['user_id'];?>">Rates</a>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>	
</div>