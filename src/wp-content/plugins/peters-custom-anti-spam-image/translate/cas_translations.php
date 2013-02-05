<?php

// Translation stuff. If your blog is in a different language, edit these fields to suit your language!
$cas_displaytext = array(); // <-- This line should not be changed

// This is the name of the label for the comment form
$cas_displaytext['label'] = 'Paraula per evitar contingut brossa: (requerit)';

// These are the instructions for filling in the anti-spam word in the comment form
$cas_displaytext['instructions'] = 'Per demostrar que ets una persona (no un robot de creació de continguts brossa), escriu la paraula de seguretat que es mostra a la imatge.';

if ($cas_wav) {
// Additional instructions for the audio-impaired users
$cas_displaytext['instructions'] .= ' Cliqueu a la imatge per sentir el fitxer d\'audio de la paraula.';
}

// Error message if someone has not typed anything into the anti-spam field
$cas_displaytext['emptyfield'] = 'S\'ha produit un error. Inseriu la paraula de segurat.';

// Error message if the particular anti-spam image has already been used on a comment
$cas_displaytext['alreadyused'] = 'S\'ha produit un error. La paraula de seguratat és invàlida. Reporteu aquest error a l\'administrador del web. Torneu enrere i refresqueu la pàgina per reenviar el vostre comentari.';

// Error message if someone has typed the wrong word into the anti-spam field
$cas_displaytext['wrongfield'] = 'S\'ha produit un error. Inseriu la paraula de seguretat correcta. Premeu el botó enrere i proveu-ho de nou.';

// Error message instructions to copy the text of the comment before pressing the back button:
$cas_displaytext['copyfield'] = 'Copieu el vostre comentari en cas que aquest lloc forci la recàrrega de la pàgina cada vegada que premeu el botó enrere:';

// Error message when trying to generate an audio file and the anti-spam image has already been used
$cas_displaytext['not_valid'] = 'Aquest número de seguretat ja no és vàlid.';

// Text to show in an invalid image
$cas_displaytext['invalid'] = '* * * INVÀLID * * *';

// Error message to point the webmaster to edit the plugin configuration settings
$cas_displaytext['manually_configure'] = 'L\'administrador del lloc necessita configurar manualment l\'adreça del lloc al fitxer de configuració de l\'extensió!';

// Error message if the GD Library is not installed
$cas_displaytext['no_gd'] = 'No s\'ha pogut inicialitzar un nou flux d\'imatge del GD';

if (!$cas_wav) {
// Text for the normal alt tag of the image
$cas_displaytext['alt_tag'] = 'Imatge de seguretat';
}
else {
// Text for the alt tag of the image for visually impaired users
$cas_displaytext['alt_tag'] = 'Cliqueu per sentir un fitxer d\'audio d\'aquesta paraula de seguretat';
}

// Error messages for the registration form only 

// Error if the particular anti-spam image has already been used
$cas_displaytext['register_alreadyused'] = 'S\'ha produit un error. La paraula de seguratat ja no és vàlida.';

// Error message if someone has typed the wrong answer into the anti-spam field
$cas_displaytext['register_wrongfield'] = 'S\'ha produit un error. Inseriu la paraula de seguretat correcta';

// Error message if someone has typed a blocked e-mail address
$cas_displaytext['register_blocked'] = 'S\'ha produit un error. Aquesta adreça de correu electrònic ha estat bloquejada.';

?>