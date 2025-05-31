package database

import (
	"errors"
	"minimvc/app/database/factory"

	"gorm.io/gorm"
)

type IDbFactory interface {
	Connect() (*gorm.DB, error)
	Close() error
}

func New(driver string) (IDbFactory, error) {
	switch driver {
	case "pgsql":
		return factory.NewPgSqlConnector(), nil
	case "mysql":
		return factory.NewMySqlConnector(), nil
	default:
		return nil, errors.New("unsupported database type " + driver)
	}
}
