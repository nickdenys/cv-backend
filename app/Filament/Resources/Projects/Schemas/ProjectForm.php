<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\File;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('handle')
                    ->label('Handle')
                    ->disabled()
                    ->helperText('The handle is auto-generated from the title and cannot be changed.'),
                RichEditor::make('description')
                    ->toolbarButtons([
                        ['undo', 'redo'],
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                        ['clearFormatting'],
                    ])
                    ->columnSpanFull(),
                TextInput::make('url')
                    ->columnSpanFull()
                    ->label('URL')
                    ->url(),
                // Using project_image_id directly; Create/Edit pages will convert a new upload path into a File record id.
                FileUpload::make('project_image_id')
                    ->columnSpanFull()
                    ->label('Project Image')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg','image/png','image/webp','image/gif'])
                    ->maxSize(5120)
                    ->afterStateHydrated(function (FileUpload $component, $state) {
                        if (blank($state)) return;
                        // If state is already a path (contains a slash), leave it.
                        if (is_string($state) && str_contains($state, '/')) return;
                        // If state is UUID of File model, swap it to object_key so FileUpload can render preview.
                        if (is_string($state)) {
                            $file = File::find($state);
                            if ($file) {
                                $component->state($file->object_key); // single file mode uses string state
                            }
                        }
                    })
            ]);
    }
}
