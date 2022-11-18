<div class="modal" id="errorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input error</h5>
            </div>
            <div class="modal-body">
                <?php if (count($errors) != 0): ?>
                    <div class='errors' style="color:red">
                        <p>Please correct the following error(s) :</p>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?=$error?></li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                <?php endif;?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Call of modal displaying input error -->
<?php if ($show_modal_error == true): ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#errorModal").modal("show");
        });
    </script>
<?php endif;?>