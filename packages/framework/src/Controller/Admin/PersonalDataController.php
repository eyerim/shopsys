<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Form\Admin\PersonalData\PersonalDataFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PersonalDataController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade
     */
    protected $adminDomainTabsFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    protected $setting;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\AdminDomainTabsFacade $adminDomainTabsFacade
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     */
    public function __construct(
        AdminDomainTabsFacade $adminDomainTabsFacade,
        Setting $setting
    ) {
        $this->adminDomainTabsFacade = $adminDomainTabsFacade;
        $this->setting = $setting;
    }

    /**
     * @Route("/personal-data/setting/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function settingAction(Request $request)
    {
        $domainId = $this->adminDomainTabsFacade->getSelectedDomainId();
        $personalDataDisplaySiteContent = $this->setting->getForDomain(
            Setting::PERSONAL_DATA_DISPLAY_SITE_CONTENT,
            $domainId
        );
        $personalDataExportSiteContent = $this->setting->getForDomain(
            Setting::PERSONAL_DATA_EXPORT_SITE_CONTENT,
            $domainId
        );

        $form = $this->createForm(
            PersonalDataFormType::class,
            [
                'personalDataDisplaySiteContent' => $personalDataDisplaySiteContent,
                'personalDataExportSiteContent' => $personalDataExportSiteContent,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setting->setForDomain(
                Setting::PERSONAL_DATA_DISPLAY_SITE_CONTENT,
                $form->getData()['personalDataDisplaySiteContent'],
                $domainId
            );
            $this->setting->setForDomain(
                Setting::PERSONAL_DATA_EXPORT_SITE_CONTENT,
                $form->getData()['personalDataExportSiteContent'],
                $domainId
            );
            $this->addSuccessFlash(t('Personal data site content updated successfully'));
        }

        return $this->render('@ShopsysFramework/Admin/Content/PersonalData/index.html.twig', [
            'form' => $form->createView(),
            'domainId' => $domainId,
        ]);
    }
}
