

                <hr>
                <div class="row">
                    <div class="col-md-12">
                    <?php
                        $finalRequest = '';
                        foreach (getImageKeys() as $key => $value) {
                            $finalRequest .= '&' . $key . '=' . urlencode($value);
                        }
                        if (strlen($finalRequest) > 0) {
                            $finalRequest[0] = '?';
                        }
                    ?>
                    <div id="imageOutput" class="text-center">
                        <?php if ($imageKeys['text'] !== '') { ?><img src="image.php<?php echo $finalRequest; ?>" alt="Barcode Image" />
                        
                        <br><br>
                        <a  href="http://www.nfnempresas.com/codigo_barra_128/image.php<?=$finalRequest?>" download="codigo_barra"><i class="fa fa-file text-primary"></i> Descargar</a>
                        
                        
                        <?php } else { ?>El código no se ha podido generar.<?php } ?>
                    </div>
                    
                    </div>
                    
                 
                    
                    
                    
                </div>
               
            
        </form>

            </div>

          </div>
            
        </div>

        <script src="jquery-1.7.2.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="barcode.js"></script>

    </body>
</html>