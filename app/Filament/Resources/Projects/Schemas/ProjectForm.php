<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                Textarea::make('description')
                    ->columnSpanFull(),
                FileUpload::make('project_image_id')
                    ->columnSpanFull()
                    ->label('Project Image')
                    ->image(),
                TextInput::make('url')
                    ->columnSpanFull()
                    ->label('URL')
                    ->url()
            ]);
    }
}
