<?php

namespace App\Filament\Pages\Concerns;

use App\Console\Commands\GenerateThumbnails;
use App\Support\Settings;
use App\Support\Thumbnails;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;

/** A Filament page whose form edits keys in the settings table. */
trait SavesSettings
{
    public ?array $data = [];

    /** Keys that are never sent back to the browser (secrets). */
    protected function hiddenKeys(): array
    {
        return [];
    }

    public function mount(): void
    {
        $this->form->fill(collect(Settings::all())->except($this->hiddenKeys())->all());
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Save changes')->submit('save')->keyBindings(['mod+s']),
                    ])->sticky(),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // An empty secret field means "keep the saved one".
        foreach ($this->hiddenKeys() as $key) {
            if (blank($data[$key] ?? null)) {
                unset($data[$key]);
            }
        }

        Settings::set($data);

        // Right-sized copies of any images uploaded here (about photo, service cards…).
        \Illuminate\Support\defer(function () {
            foreach (GenerateThumbnails::allImagePaths() as $path) {
                Thumbnails::generateAll($path);
            }
        });

        Notification::make()->title('Saved')->body('Your changes are live on the website.')->success()->send();
    }
}
