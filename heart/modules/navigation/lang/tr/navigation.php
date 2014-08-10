<?php
// Language file for navigation module

return array(

	'title' => 'Navigasyon Yönetimi',

	'desc' => 'Sitenizdeki navigasyon bağlantı ve gruplarını yönetin.',

	'menu' => 'Navigasyon',

	'link'	=> array(
		'title'		=> 'Navigasyon',
		'save'		=> 'Kaydet',
		'add'		=> 'Yeni link ekle',
		'edit'		=> 'Düzenle',
		'delete'	=> 'Sil',
		'no_links'	=> 'Navigasyon grubuna ait bir link yok!',
	),

	'group'	=> array(
		'title'		=> 'Navigasyon Grup "%s"',
		'add'		=> 'Yeni Grup Oluştur',
		'edit'		=> 'Grubu Düzenle',
		'delete'	=> 'Grubu Sil',
		'no_have'	=> 'Hiç navigasyon grubu yok!',
	),

	'labels' => array(
		'title' 		=> 'Navigasyon Başlğı',
		'type' 			=> 'Navigasyon Tipi',
		'uri' 			=> 'Link',
		'page' 			=> 'Sayfa',
		'module' 		=> 'Modül',
		'url' 			=> 'URL',
		'position'		=> 'Navigasyon Pozisyonu',
		'group'			=> 'Navigasyon Grubu',
		'target'		=> 'Link Hedefi',
		'class'			=> 'Bu link için bir class',
		'target_normal'	=> 'Mevcut pencerede',
		'target_new_win'=> 'Yeni Pencerede (_blank)',
	),

	'group_labels' => array(
		'name' => 'Adı',
		'slug' => 'Yayın Adı',
	),

	'message'	=> array(
		'csrf_error'		=> 'CSRF Anahtarı geçersiz!',
		'create_success'	=> 'Navigasyon linki başarıyla oluşturuldu.',
		'create_error'		=> 'Navigasyon anahtarı oluşturulurken bir hata oluştu.',
		'edit_success'		=> 'Navigasyon linki başarıyla düzenlendi.',
		'edit_error'		=> 'Navigasyon linki düzenlenirken bir hata oluştu.',
		'delete_success'	=> 'Navigasyon linki başarıyla silindi.',
		'delete_error'		=> 'Navigasyon linki silinirken bir hata oluştu.',
		'exists'			=> 'Grup adı zaten mevcut!',
		'required'			=> 'Grup adı zorunludur!',
		'group_create_success' => 'Grup %s başarıyla oluşturuldu.',
		'group_create_error' => 'Grup oluşturulurken bir hata oluştu %s!',
	),

	'toolbar'	=> array(
			'link' => 'Linkler',
			'link_info' => 'Navigasyon linklerini görüntüle',
			'group' => 'Grup',
			'group_info' => 'Navigasyon Grubunu Görüntüle'
		)
);

// end of navigation.php
