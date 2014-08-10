<?php

return array(
	'comment' 				=> array(
		'sig' 				=> 'Yorum',
		'plu' 				=>'Yorumlar'
	),

	'empty' 				=> array(
		'database' 			=> 'veritabanı boş',
		'id' 				=> 'geçersiz id ',
	),

	'title' 				=> 'Yorumlar Modülü',
	'edit' 					=> 'Düzenle',
	'reply' 				=> 'Cevapla',
	'no_comment' 			=> 'Böyle bir {:type} yorum yok.',
	'message' 				=> array(
		'success' 			=> array(
			'change_status' => 'Yorum durumu başarıyla değiştirildi',
			'multi_acion'	=> 'Yorum başarıyla {:method}',
			'multi_actions'	=> 'Comments successfully {:method}',
			'delete'		=> 'Yorum başarıyla silindi',
			'multi_delete'	=> 'Comments successfully silindi',
			'comment_submit'=> 'Yorum başarıyla gönderilidi',
		),
		'error'				=> array(
			'general' 		=> 'Hata oluştu! Birşeyler ters gitti',
			'not_exist' 	=> 'Böyle bir yorum bulunamadı',
			'multi_action'	=> 'Geçerli işlem gerçekleştirilemedi {:method}',
			'delete'		=> 'Hata oluştu yorum silinemedi',
			'comment_submit'=> 'Yorum gönderilemedi',
			'bot'			=> 'Bu yorum muhtemelen gerçek bir kişi tarafından yazılamadı.',
			'validation'	=> 'Doğrulama Hatası!!!',
			'parent_not_approved' => 'Üzgünüz, bu yorumu onaylayamadınız çünkü üst yorum buna izin vermiyor..',
		),
		'warning' 			=> array(
			'approve_spam'	=> 'Spam olan bir yorumu onaylıyorsunuz.',
			'no-akismet-key'=> 'Lütfen akismet api anahtarınızı girin.',
			'wrong-akismet'	=> 'Yanlış bir akismet anahtarı girdiniz.',
			'muti-action-note' => '*** Not : Yaptığınız tüm işlemler çocuk yorumları da etkileyecek',
		),
		'wait_approve' 		=> 'Yorumunuz onay bekliyor.',
		'no_comment' 		=> 'Bu içerik için hiç yorum yazılmamış.',
		'single_comment' 	=> '1 yorum yazılmış.',
		'multi_comment' 	=> '{:num} yorum yazılmış.',
	),
	'multi' 				=> array(
		'approve' 			=> 'Yorumlar onaylandı',
		'unapprove' 		=> 'Yorumlar onaylanmadı',
		'delete' 			=> 'Yorumlar silindi',
	),

	'menu'					=> array(
		'approved'			=> 'Onaylanmış',
		'pending'			=> 'Onay bekliyor',
	),

	'info'					=> array(
		'approve'			=> 'Onayla',
		'unapprove'			=> 'Onayı Kaldır',
	),

	'label'					=> array(
		'all'				=> 'Tümü',
		'approved'			=> 'Onaylanmışlar',
		'unapproved'		=> 'Onaylanmamışlar',
		'pending'			=> 'Onay Bekleyenler',
		'spam'				=> 'Spam',
		'author_email'		=> 'Yazar e-posta adresi',
		'message'			=> 'Mesaj',
		'status'			=> 'Durumu',
		'edit_info'			=> 'Son Düzenleyen: {:name}',
		'reply'				=> 'Cevapla',
		'mark_as_spam'		=> 'Spam olarak işaretle',
		'apply_selected'	=> 'Bu işlemi tüm seçilenlere uygula',
		'select_action'		=> '-- bir işlem seçin --',
		'post_comment'		=> 'Yorum gönder',
		'reply_to'			=> '{:name} isimli kişinin yorumunu onayla',
		'edit_comment'		=> '{:name} isimli kişinin yorumunu düzenle',
		'post_comment_as'	=> 'Şu işmi kullanarak yorum yap: {:name}.',
		'logout'			=> 'Çıkış',
	),

	'form' 					=> array(
		'name' 				=> 'Ad',
		'email' 			=> 'E-Posta',
		'url' 				=> 'Web Site',
		'comment' 			=> 'Yorumlar',
		'action' 			=> 'İşlem',
		'date' 				=> 'Tarih',
		'pending' 			=> 'Onay Bekliyor',
		'submit' 			=> 'Gönder',
		'cancel' 			=> 'İptal',
		'all' 				=> 'Tümü'
	),
);
