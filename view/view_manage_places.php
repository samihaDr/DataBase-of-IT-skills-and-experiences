<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage places</title>
    <base href="<?=$web_root?>"/>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="css/places.css">
    <link rel="icon" type="image/png" sizes="32x32" href="image/cv-icon.png">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
</head>
<body>
    <?php include 'view/menu.php';?>
    <div class="main">

        <h1>Manage places</h1>

        <table class="table table-hover container-fluid">
            <thead>
                <tr>
                    <th class="input_text_column">Name</th>
                    <th class="input_text_column">City</th>
                    <th class="action_buttons">Actions</th>
                    <th class="info_references">Infos</th>
                </tr>
            </thead>
            
            <tbody>
                <tr>
                    <td>
                        <form id="place_form" method="post" hidden></form>
                        <input form="place_form" type="text" class="form-control" name="name" placeholder="Name" value="<?=$name?>">
                    </td>
                    
                    <td>
                        <input form="place_form" type="text" class="form-control" name="city" placeholder="City" value="<?=$city?>">
                    </td>
                    
                    <td colspan="2">
                        <button form="place_form" type="submit" formaction="place/add_place" class="btn btn-outline-success manage">
                            <img src="image/add.png" alt="add_button">
                        </button>
                    </td>
                </tr>

                <?php $idx = 0; ?>
                <?php foreach($places as $place): ?>
                    <tr>
                        <!-- disabled inputs and buttons if place is referenced in other tables -->
                        <?php if($experiences_by_place_counter[$idx]>0): ?>
                            <td>
                                <form id="place_form<?=$idx?>" method="post" hidden></form>
                                <input type="text" class="form-control" value="<?=$place->name?>" disabled>
                            </td>
                            
                            <td>
                                <input type="text" class="form-control" value="<?=$place->city?>" disabled>
                            </td>
                            
                            <td class="buttons_edition">
                                <button type="submit" form="place_form<?=$idx?>" class="btn btn-outline-primary manage" disabled>
                                    <img src="image/edit.png" alt="edit_button">
                                </button>
                                <button type="submit" form="place_form<?=$idx?>" class="btn btn-outline-danger manage" disabled>
                                    <img src="image/delete.png" alt="delete_button">
                                </button>
                            </td>
                        
                        <!-- enabled inputs and buttons if place is not referenced in other tables -->
                        <?php else: ?>
                            <td>
                                <form id="place_form<?=$idx?>" method="post" hidden></form>
                                <input type="text" form="place_form<?=$idx?>" class="form-control" name="name_edit" value="<?=$place->name?>">
                            </td>
                            
                            <td>
                                <input type="text" form="place_form<?=$idx?>" class="form-control" name="city_edit" value="<?=$place->city?>">
                            </td>
                            
                            <td class="buttons_edition">
                                <input type="hidden" name="place_id" form="place_form<?=$idx?>" value ="<?=$place->id?>">
                                <button type="submit" form="place_form<?=$idx?>" formaction="place/edit_place" class="btn btn-outline-primary manage">
                                    <img src="image/edit.png" alt="edit_button">
                                </button>
                                
                                <a href="place/delete_confirm_place/<?=$place->id?>" class="btn btn-outline-danger" role="button" aria-pressed="true">
                                    <img src="image/delete.png" alt="delete_button">
                                </a>
                            </td>
                            
                        <?php endif; ?>
                        
                        <td>
                            Used in <?=$experiences_by_place_counter[$idx]?> experience(s).
                        </td>
                    </tr> 
                    
                    <?php $idx++; ?>
                <?php endforeach;?>
            </tbody> 
            
        </table>
        
        <!-- Modal displaying errors -->
        <?php include 'view/view_errors.php'; ?>
    </div>
</body>
</html>