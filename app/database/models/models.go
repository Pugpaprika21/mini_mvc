package models

import "gorm.io/gorm"

type IModels interface{}

type models struct {
	db *gorm.DB
}

func New(db *gorm.DB) IModels {
	return &models{db: db}
}
