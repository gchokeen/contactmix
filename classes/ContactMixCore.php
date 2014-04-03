<?php
class ContactMixCore{
    
    public function __construct(){
        
    }
    
    public function getGeneralOptions(){
        
        return get_option( 'contactmix_general_settings' );
    }
    
    
    
    
    
}