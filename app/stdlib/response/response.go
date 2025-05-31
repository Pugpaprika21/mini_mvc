package response

import (
	"github.com/labstack/echo/v4"
)

type Response struct {
	Message    string      `json:"message,omitempty"`
	Code       int         `json:"code,omitempty"`
	StatusBool bool        `json:"status_bool,omitempty"`
	Data       interface{} `json:"data,omitempty"`
	ErrorCode  string      `json:"error_code,omitempty"`
	Timestamp  string      `json:"timestamp,omitempty"`
	Path       string      `json:"path,omitempty"`
	RequestID  string      `json:"request_id,omitempty"`
	Total      int         `json:"total,omitempty"`
	View       echo.Map    `json:"view,omitempty"`
}

type builderResponse struct {
	messageX    string
	codeX       int
	dataX       interface{}
	statusBoolx bool
	totalX      int
	viewX       echo.Map
}

func NewBuilder() *builderResponse {
	return &builderResponse{}
}

func (r *builderResponse) Message(message string) *builderResponse {
	r.messageX = message
	return r
}

func (r *builderResponse) Code(code int) *builderResponse {
	r.codeX = code
	return r
}

func (r *builderResponse) Data(data interface{}) *builderResponse {
	r.dataX = data
	return r
}

func (r *builderResponse) Bool(statusbool bool) *builderResponse {
	r.statusBoolx = statusbool
	return r
}

func (r *builderResponse) Total(total int) *builderResponse {
	r.totalX = total
	return r
}

func (r *builderResponse) Build() *Response {
	return &Response{
		Message:    r.messageX,
		Code:       r.codeX,
		Data:       r.dataX,
		StatusBool: r.statusBoolx,
		Total:      r.totalX,
		View:       r.viewX,
	}
}

// View
func (r *builderResponse) ViewErr(err error) *builderResponse {
	r.viewX = echo.Map{
		"title": r.messageX,
		"error": err.Error(),
	}
	return r
}

func (r *builderResponse) Response() echo.Map {
	return r.viewX
}
