/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  hendri
 * Created: Apr 29, 2018
 */

-- alter user
ALTER TABLE `user`
ADD `deleted_at` timestamp NULL AFTER `apoteker_sik`;