//
//  AbstractObject.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/28.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import Alamofire
import Alamofire_SwiftyJSON
import SwiftyJSON

class AbstractObject: NSObject {
    enum Pramas: String {
        case params = "params"
        enum Platform: String {
            case ios = "ios"
        }
    }
    enum Request: String {
        case deviceToken = "device_token"
        case unigueId = "unique_id"
        case platform = "platform"
        case beacon = "beacon_locations"
        case geo = "geo_locations"
        
        enum Beacon: String {
            case time = "time"
            case name = "name"
            case level = "level"
            
            func getKey (_ index:Int) -> String {
                return "\(Request.beacon.rawValue)[\(index)][\(self.rawValue)]"
            }
        }
        
        enum Geo: String {
            case latitude = "lat"
            case longitude = "lon"
            func getKey () -> String {
                return "\(Request.geo.rawValue)[\(self.rawValue)]"
            }
        }
    }
    
    enum Response: String {
        case result = "result"
        case message = "message"
        case uniqueId = "unique_id"
        case checkFlag = "check_flag"
        case button = "button"
        
        enum Receipt:String {
            case createdAt = "created_at"
            case registedAt = "registed_at"
        }
        
        enum Settings: String {
            case beaconSendSpan = "beacon_send_span"
            case beaconCollectSpan = "beacon_collect_span"
            case geoSendSpan = "geo_send_span"
            case activeSendTime = "active_send_time"
            case activeReceiptTime = "active_receipt_time"
            case beaconUuidList = "beacon_uuid_list"
            case menuCheckSpan = "menu_check_span"
            
            enum ActiveSendTime: String {
                case start = "start"
                case end = "end"
            }
            
            enum ActiveReceiptTime: String {
                case start = "start"
                case end = "end"
            }
        }
    }
    
    var result:Bool = false
    var message:String = ""
    
    func setData(response:DataResponse<JSON>){
        print(response)
        if let resultData = response.result.value {
            let json = JSON(resultData)
            
            if let result = json[Response.result.rawValue].bool {
                self.result = result
                if let m = json[Response.message.rawValue].string {
                    self.message = m
                }
                if result {
                    abstractSetData(json: json);
                }
            } else {
                // result がない場合もある。
                // ない場合は true とする
                self.result = true
                abstractSetData(json: json);
            }
        } else {
//            print(response.result.error)
            let e:Error? = response.result.error
            if (e?.code == -1009) {
                self.message = "インターネットに接続されていません"
            } else {
                self.message = "通信エラー"
            }
            
            self.result = false
        }

    }
    
    
    func abstractSetData(json:JSON) {
        
    }
}

extension Error {
    var code: Int { return (self as NSError).code }
    var domain: String { return (self as NSError).domain }
}
