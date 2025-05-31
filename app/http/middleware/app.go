package middleware

import (
	"github.com/labstack/echo/v4"
	"github.com/labstack/echo/v4/middleware"
)

func LoggerWithConfig() echo.MiddlewareFunc {
	return middleware.LoggerWithConfig(middleware.LoggerConfig{
		Format: "\033[36m${time_rfc3339}\033[0m | \033[32m${method}\033[0m | ${uri} | status=\033[31m${status}\033[0m | error=\033[31m${error}\033[0m\n",
	})
}
