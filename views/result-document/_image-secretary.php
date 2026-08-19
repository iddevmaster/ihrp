<?php
if (!isset($submission->secretary_person) || !isset($submission->secretaryPerson->person)) {
    return;
}
$person = $submission->secretaryPerson->person;
$signType = ($type == 'thai') ? 'thai' : 'eng';
$signPath = ($type == 'thai') ? $person->templatePathAliasSignatureThai : $person->templatePathAliasSignature;
$signValue = ($type == 'thai') ? $person->signature_thai : $person->signature;
?>
<?php if (!empty($signValue) && file_exists($signPath)) { ?>
    <?php
    $rawData = $person->getDecryptedSignatureData($signType);
    if ($rawData !== false):
        $imageData = base64_encode($rawData);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($rawData) ?: 'image/png';
        ?>
        <p style="padding-left: 250px; padding-bottom: -50px;"><img src="data:<?= $mimeType ?>;base64,<?= $imageData ?>" /></p>
    <?php endif; ?>
<?php } ?>
