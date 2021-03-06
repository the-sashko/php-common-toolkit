<?php
class LanguageVendor
{
    public function convertPo2Mo(
        ?string $poFilePath = null,
        ?string $moFilePath = null
    ): void
    {
        if (empty($poFilePath)) {
            throw new LanguageException(
                'PO File Path Is Not Set',
                LanguageException::PO_FILE_IS_NOT_SET
            );
        }

        if (!file_exists($poFilePath) || !is_file($poFilePath)) {
            throw new LanguageException(
                'PO File Path Is Not Set',
                LanguageException::PO_FILE_IS_NOT_EXISTS
            );
        }

        if (empty($moFilePath)) {
            throw new LanguageException('MO File Path Is Not Set', LanguageException::MO_FILE_IS_NOT_SET
            );
        }

        if (file_exists($moFilePath) && is_file($moFilePath)) {
            unlink($moFilePath);
        }

        $execString = sprintf('msgfmt %s -o %s', $poFilePath, $moFilePath);
        exec($execString);

        chmod($moFilePath, 0775);
    }
}
