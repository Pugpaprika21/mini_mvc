package factory

import (
	"database/sql"
	"fmt"
	"log"
	"net/url"
	"os"
	"sync"
	"time"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"
	"gorm.io/gorm/logger"
)

type mySqlConnector struct {
	db    *gorm.DB
	sqlDB *sql.DB
}

var mysqlInstance *mySqlConnector
var mysqlOnce sync.Once

func NewMySqlConnector() *mySqlConnector {
	mysqlOnce.Do(func() {
		mysqlInstance = &mySqlConnector{}
	})
	return mysqlInstance
}

func (m *mySqlConnector) Connect() (*gorm.DB, error) {
	var err error
	dsn := fmt.Sprintf("%s:%s@tcp(%s:%s)/%s?parseTime=true&loc=%s",
		os.Getenv("DB_USERNAME"),
		os.Getenv("DB_PASSWORD"),
		os.Getenv("DB_HOST"),
		os.Getenv("DB_PORT"),
		os.Getenv("DB_NAME"),
		url.QueryEscape(os.Getenv("DB_TIMEZONE")),
	)

	newLogger := logger.New(
		log.New(os.Stdout, "\r\n", log.LstdFlags),
		logger.Config{
			SlowThreshold:             200 * time.Millisecond,
			LogLevel:                  logger.Info,
			IgnoreRecordNotFoundError: true,
			Colorful:                  true,
		},
	)

	m.db, err = gorm.Open(mysql.Open(dsn), &gorm.Config{
		Logger: newLogger,
	})
	if err != nil {
		return nil, err
	}

	m.sqlDB, err = m.db.DB()
	if err != nil {
		return nil, err
	}

	m.sqlDB.SetMaxIdleConns(10)
	m.sqlDB.SetMaxOpenConns(100)

	return m.db, nil
}

func (m *mySqlConnector) Close() error {
	if m.sqlDB != nil {
		return m.sqlDB.Close()
	}
	return nil
}
