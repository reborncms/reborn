<?php

return array(
	'comment' 				=> array(
		'sig' 				=> 'မှတ်ချက်',
		'plu' 				=>'မှတ်ချက်များ '
	),

	'empty' 				=> array(
		'database' 			=> 'too bad it\'s empty',
		'id' 				=> 'invalid id ',
	),

	'title' 				=> 'Comments Module',
	'edit' 					=> 'Edit',
	'reply' 				=> 'Reply',
	'no_comment' 			=> 'There is no {:type} comment.',
	'message' 				=> array(
		'success' 			=> array(
			'change_status' => 'မှတ်ချက် အခြေအနေကို အောင်မြင်စွာ ပြောင်းလဲပြီးသည်။',
			'multi_action'	=> 'Comment successfully {:method}',
			'multi_actions'	=> 'Comments successfully {:method}',
			'delete'		=> 'မှတ်ချက်အား အောင်မြင်စွာ ဖျက်ပြီးသည်။',
			'multi_delete'	=> 'မှတ်ချက်များအား အောင်မြင်စွာ ဖျက်ပြီးသည်။',
			'comment_submit'=> 'မှတ်ချက်အား အောင်မြင်စွာ ပေးပို့ပြီးသည်။',
		),
		'error'				=> array(
			'general' 		=> 'Error Occur. Something went wrong.',
			'not_exist' 	=> 'Sorry this comment is not appear to exist',
			'multi_action'	=> 'Error occur cannot {:method} comment',
			'delete'		=> 'မှတ်ချက်အား ဖျက်ရာတွင် တစ်ခုခုမှားယွင်းနေသည်။',
			'comment_submit'=> 'မှတ်ချက်အား ပေးပို့၍ မရပါ။',
			'bot'			=> 'This is probably a bot.',
			'validation'	=> 'Validation Error!!!',
			'parent_not_approved' => 'parent comment ကို အတည်ပြုထားခြင်းမရှိသည့် အတွက် ဒီ မှတ်ချက်ကို အတည်ပြု၍မရပါ။',
		),
		'warning' 			=> array(
			'approve_spam'	=> 'spam မှတ်ချက်အား သင် အတည်ပြုလိုက်သည်။',
			'no-akismet-key'=> 'spam filter အလုပ်လုပ်ဖို့အတွက် akismet api key ကို မှတ်ချက် ပြင်ဆင်ရန် တွင် ထည့်ပေးပါ။',
			'wrong-akismet'	=> 'akismet api key မှားနေပါသည်။',
			'muti-action-note' => '*** မှတ်ချက်။ ။ parent comment တွင် လုပ်ဆောင်ချက်များသည် child comment များတွင်လညး် သက်ရောက်သည်။ (ဥပမာ။ ။ အခြေအနေ ပြောင်းခြင်း။ ဖျက်ခြင်း။)
',
		),
		'wait_approve' 		=> 'သင့် မှတ်ချက်အား admin မှ အတည်ပြုရန် စောင့်ဆိုင်းထားသည်။',
		'no_comment' 		=> 'မှတ်ချက်မရှိပါ။',
		'single_comment' 	=> 'မှတ်ချက် တစ်ခုရှိသည်။',
		'multi_comment' 	=> 'မှတ်ချက် {:num} ခုရှိသည်။',
	),
	'multi' 				=> array(
		'approve' 			=> 'မှတ်ချက်များကို အတည်ပြုလိုက်သည်။',
		'unapprove' 		=> 'မှတ်ချက်များကို အတည်မပြုတော့ပါ။',
		'delete' 			=> 'မှတ်ချက်များကို ဖျက်လိုက်သည်။',
	),

	'menu'					=> array(
		'approved'			=> 'အတည်ပြုထားသော မှတ်ချက်များ',
		'pending'			=> 'အတည်မပြုရသေးသော မှတ်ချက်များ',
	),

	'info'					=> array(
		'approve'			=> 'အတည်ပြုမည်။',
		'unapprove'			=> 'အတည်မပြုတော့ပါ။',
	),

	'label'					=> array(
		'all'				=> 'မှတ်ချက် အားလုံး',
		'approved'			=> 'အတည်ပြုပြီး',
		'unapproved'		=> 'အတည်မပြုတော့ပါ။',
		'pending'			=> 'အတည်မပြုရသေး',
		'spam'				=> 'Spam',
		'author_email'		=> 'ရေးသားသူ အီးမေးလ်',
		'message'			=> 'အကြောင်းအရာ',
		'status'			=> 'အခြေအနေ',
		'edit_info'			=> '{:name} မှ နောက်ဆုံးပြုပြင်ထားသည်။',
		'reply'				=> 'ပြန်ဖြေကြားမည်',
		'mark_as_spam'		=> 'spam အဖြစ် သတ်မှတ်မည်။',
		'apply_selected'	=> 'ရွေးချယ်ထားသည်များကို ပြုလုပ်မည်',
		'select_action'		=> '-- ပြုလုပ်ချင်တာကို ရွေးရန် --',
		'post_comment'		=> 'မှတ်ချက် ပေးရန်',
		'reply_to'			=> '{:name} ၏ မှတ်ချက်အား ပြန်ဖြေကြားမည်။',
		'edit_comment'		=> '{:name} ၏ မှတ်ချက်အား ပြင်ဆင် မည်။',
		'post_comment_as'	=> '{:name} အဖြစ် မှတ်ချက်တင်မည်။',
		'logout'			=> 'Logout',
	),
);
