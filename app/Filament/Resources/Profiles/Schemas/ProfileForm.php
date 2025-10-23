<?php

namespace App\Filament\Resources\Profiles\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;

class ProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                RichEditor::make('bio')
                    ->toolbarButtons([
                        ['undo', 'redo'],
                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                        ['clearFormatting'],
                    ])
                    ->columnSpanFull(),
                Repeater::make('links')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('url')
                            ->label('URL')
                            ->maxLength(255)
                            ->required()
                            ->url()
                    ])
                    ->grid(2)
                    ->columnSpanFull()
                    ->addActionLabel('Add link')
                    ->addActionAlignment(Alignment::Start)
                    ->reorderable()
                    ->cloneable()
                    ->default([]),
            ]);
    }
}
