<?php

namespace Botble\Mailing\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Mailing\Forms\ContactForm;
use Botble\Mailing\Http\Requests\ContactRequest;
use Botble\Mailing\Models\Contact;
use Botble\Mailing\Tables\ContactTable;

class ContactController extends BaseController
{
    public function index(ContactTable $table)
    {
        $this->pageTitle(trans('plugins/mailing::mailing.contacts.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/mailing::mailing.contacts.create'));

        return ContactForm::create()->renderForm();
    }

    public function store(ContactRequest $request)
    {
        $form = ContactForm::create()->setRequest($request);
        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('mailing.contacts.index'))
            ->setNextUrl(route('mailing.contacts.edit', $form->getModel()->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(Contact $contact)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $contact->name ?: $contact->email]));

        return ContactForm::createFromModel($contact)->renderForm();
    }

    public function update(Contact $contact, ContactRequest $request)
    {
        $form = ContactForm::createFromModel($contact)->setRequest($request);
        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('mailing.contacts.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Contact $contact)
    {
        return DeleteResourceAction::make($contact);
    }
}
