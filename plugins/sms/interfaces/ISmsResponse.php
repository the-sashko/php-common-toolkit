<?php
interface ISmsResponse
{
    public function getStatus(): bool;

    public function getErrorMessage(): ?string;

    public function getRemoteMessageCode(): ?string;
}
