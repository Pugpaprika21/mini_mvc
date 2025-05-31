package services

import "minimvc/app/database/models"

type IServices interface{}

type services struct {
	models models.IModels
}

func New(models models.IModels) IServices {
	return &services{models: models}
}
