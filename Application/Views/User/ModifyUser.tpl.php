<div>
        <!--<center>-->
       
    <h3><?php echo Class_message::get('msgTitleRestoreData')?></h3>
            <form>
                
                <b><label class="blue"> <?php echo Class_message::get('msgNames')?></label></b> <input type="text" value="<?php $user= new UserLogin(); echo $user->get_names()." ". $user->get_lastnames()?>">
                <b><label class="blue"> <?php echo Class_message::get('TxtUserName')?></label></b> <input type="text" value="<?php  echo $user->get_userName()?>">
                <b><label class="blue"><?php echo Class_message::get('msgPasswordNew')?></label></b> <input type="password">
                <b><label class="blue"> <?php echo Class_message::get('msgTextConfirmPasswordUser')?></label></b> <input type="password">
                <button type="submit" > <?php echo Class_message::get('BtnAccept')?></button>
                <button type="reset"> <?php echo Class_message::get('BtnClear')?></button>
            </form>
        <!--</center>-->
       
        
   
</div>

