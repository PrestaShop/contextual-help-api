locals {
  gcp_project                     = local.config.gcp_project[terraform.workspace]
  mapped_domain                   = local.config.mapped_domain[terraform.workspace]

  config = {
    gcp_project = {
      integration = "core-oss-integration" 
      preprod     = "core-oss-preproduction"
      production  = "core-oss-production"
    }
    mapped_domain = {
      integration = "integration-help.prestashop-project.org"
      preprod     = "preprod-help.prestashop-project.org"
      production  = "help.prestashop-project.org"
    }
  }
}

variable "app_version" {
  description = "API app tag version"
  default     = "latest"
}

variable "hash_id" {
  description = "Github hash"
  default     = "latest"
}
