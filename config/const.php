<?php

return [

   'notificationType' => ['1' => 'Product Sold', '2' => 'Review Given', '3' => 'Review Updated', '4' => 'New Message', '5' => 'New User Registered'],
   'user_roles'=> [2 => 'User'],
   'item_types'=> [1 => 'In-Game Items'],
   'order_types'=> [1 => 'All Orders' , 2 => 'My Orders', 3 => 'Sold Products'],
   'delivery_methods'=> [1 => 'Yes',2 => 'No'],
   'payment_status'=> [1 => 'Pending', 2 => 'Success', 3 => 'Cancled'],
   'payment_methods'=> [1 => 'Credit Card', 2 => 'Paypal'],
   'estimate_delivery_time' => [
      ['id' => 1, 'value' =>  '1-2 days'], 
      ['id' => 2, 'value' => '3-5 days'],
      ['id' => 3, 'value' => '1 week or more']
   ],
   'payment_status' => [
      ['COMPLETED' => 'The funds for this captured payment were credited to the payee PayPal account.'],
      ['DECLINED' => 'The funds could not be captured.'],
      ['PARTIALLY_REFUNDED' => 'An amount less than this captured payment amount was partially refunded to the payer.'],
      ['PENDING' => 'The funds for this captured payment was not yet credited to the payees PayPal account. For more information, see status.details.'],
      ['REFUNDED' => 'An amount greater than or equal to this captured payment amount was refunded to the payer.'],
      ['FAILED' => 'There was an error while capturing payment.']
   ],
   'order_status' => [
      ['CREATED' => 'The order was created with the specified context'],
      ['SAVED' => 'The order was saved and persisted. The order status continues to be in progress until a capture is made with final_capture = true for all purchase units within the order.'],
      ['APPROVED' => 'The customer approved the payment through the PayPal wallet or another form of guest or unbranded payment. For example, a card, bank account, or so on.'],
      ['VOIDED' => 'All purchase units in the order are voided.'],
      ['COMPLETED' => 'The payment was authorized or the authorized payment was captured for the order.'],
      ['PAYER_ACTION_REQUIRED' => 'The order requires an action from the payer (e.g. 3DS authentication). Redirect the payer to the "rel":"payer-action" HATEOAS link returned as part of the response prior to authorizing or capturing the order.' ]
   ],
   'transaction_status' => [
      ['SUCCESS' => 'Funds have been credited to the recipient’s account.'],
      ['FAILED' => 'This payout request has failed, so funds were not deducted from the sender’s account.'],
      ['PENDING' => 'Payout request has been submitted and is being processed. Recipient will get the funds once the request is processed successfully, else the funds will be returned to you.'],
      ['UNCLAIMED' => 'The recipient for this payout does not have a PayPal account. A link to sign up for a PayPal account was sent to the recipient. However, if the recipient does not claim this payout within 30 days, the funds are returned to your account.'],
      ['RETURNED' => 'The recipient has not claimed this payout, so the funds have been returned to your account.'],
      ['ONHOLD' => 'This payout request is being reviewed and is on hold.'],
      ['BLOCKED' => 'This payout request has been blocked.'],
      ['REFUNDED' => 'This payout request was refunded.'],
      ['REVERSED' => 'This payout request was reversed.']
   ],
   'batch_status' => [
      ['DENIED' => 'Your payout requests were denied, so they were not processed. Check the error messages to see any steps necessary to fix these issues.'],
      ['PENDING' => 'Your payout requests were received and will be processed soon.'],
      ['PROCESSING' => 'Your payout requests were received and are now being processed.'],
      ['SUCCESS' => 'Your payout batch was processed and completed. Check the status of each item for any holds or unclaimed transactions.'],
      ['CANCELED' => 'The payouts file that was uploaded through the PayPal portal was cancelled by the sender.']
   ],
   'pages'=> [1 => 'Home Page' , 2 => 'About Us', 3 => 'Terms & Conditions', 4=>'Faq',5=>'Feature', 6=>'Privacy Policy',7=>'Contact Us']
];
