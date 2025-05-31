package main

import (
	"log"
	"minimvc/app/database"
	"minimvc/app/database/models"
	"minimvc/app/http/controllers"
	"minimvc/app/router"
	"minimvc/app/stdlib/jwtx"
	"minimvc/app/stdlib/views"

	"minimvc/app/http/services"
	"os"

	"github.com/joho/godotenv"
)

func main() {
	err := godotenv.Load("../_dev.env")
	if err != nil {
		log.Fatalf("Error loading .env file")
	}

	connector, err := database.New(os.Getenv("DB_DRIVER"))
	if err != nil {
		log.Fatalf("Error establishing database connection: %s", err)
	}

	db, err := connector.Connect()
	if err != nil {
		log.Fatalf("Error establishing database connection: %s", err)
	}

	defer func() {
		if err := connector.Close(); err != nil {
			log.Printf("Error closing database connection: %s", err)
		}
	}()

	var models models.IModels = models.New(db)
	var services services.IServices = services.New(models)
	var controllers controllers.IControllers = controllers.New(services)

	jwtx := jwtx.New()

	views, err := views.New()
	if err != nil {
		log.Fatalf("Error Cannot loading Views file: %v", err)
	}

	router := router.New(&router.Config{
		Jwtx:        jwtx,
		Controllers: controllers,
		Views:       views,
	})

	router.Start()
}
