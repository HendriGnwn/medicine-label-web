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


-- edit no_resep ke varchat 50
ALTER TABLE `mm_transaksi_add_obat`
CHANGE `no_resep` `no_resep` varchar(50) NULL AFTER `harga`;


-- user last login at
ALTER TABLE `user`
ADD `last_login_at` timestamp NULL;