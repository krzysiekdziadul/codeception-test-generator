<?php


namespace CodeceptionTestsGenerator;


class Init extends FrameworkPrepare
{
    public function initial()
    {
        $init = new FrameworkPrepare();

        $init->colectionFolder();
        $init->commandFolder();
        $init->copyCusomCommand();
        $init->bootstrap();
        $init->removeAcceptanceTester();
    }
}