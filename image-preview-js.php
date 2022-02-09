/**HTML**--------------------------------------------------------------------------------------------------------------------------------------------/		
						<div class="mb-4 row input_fields_wrap">
                                                    <label for="title">Color*</label>
                                                    <div class="col-sm-10">
                                                        <input type="color" class="form-control" required name="color[]" placeholder="Enter your color..." required>
                                                        <label class="mt-2">Image*</label>
                                                        <div class="form-file mb-4">
                                                            <input type="file" class="form-file-input " id="customFile" data-id="1" name="color_images[]" onchange="loadFile(this)" required>
                                                            <label class="form-file-label" for="customFile">
                                                                <span class="form-file-text">Choose file...</span>
                                                                <span class="form-file-button">Browse</span>
                                                            </label>
                                                            <div class="imagediv1"></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2" >
                                                        <button class="add_field_button btn btn-pill btn-outline-success form-control" style="margin-top: 0;!important">Add More Color</button>
                                                    </div>
                                                </div>
/**HTML**--------------------------------------------------------------------------------------------------------------------------------------------/

/*image preview starts JS*-----------------------------------------------------------------------------------------------------------------------------/
    var loadFile = function(aug) {
        let id = aug.dataset.id;
        $(".imagediv"+id).children().remove();
        // console.log(aug.dataset.id);


        for (var i=0; i<aug.files.length; i++)
        {
            var reader = new FileReader();
            reader.onload = function(event)
            {
                $($.parseHTML('<img class="img-fluid image-preview" width="200" height="200">')).attr('src', event.target.result).appendTo('.imagediv'+id);
            }
            reader.readAsDataURL(aug.files[i]);
        }
    };
/*image preview ends JS*-------------------------------------------------------------------------------------------------------------------------------/