//
//  SettingsObject.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/28.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import SwiftyJSON

class SettingsObject: AbstractObject {
    
    enum DateFormat:String {
        case standard = "yyyy-MM-dd H:mm"
        case yyyMMdd = "yyyy-MM-dd"
    }
    
    override func abstractSetData(json: JSON) {
        // ビーコン送信間隔
        self.beaconSendSpan = json[Response.Settings.beaconSendSpan.rawValue].int!
        
        // ビーコン収集時間
        self.beaconCollectSpan = json[Response.Settings.beaconCollectSpan.rawValue].int
        
        // 位置情報送信間隔
        self.geoSendSpan = json[Response.Settings.geoSendSpan.rawValue].int
        
        // 送信可能時間帯
        let activeSendTime = json[Response.Settings.activeSendTime.rawValue]
        self.activeSendTime.start = activeSendTime[Response.Settings.ActiveSendTime.start.rawValue].string
        self.activeSendTime.end = activeSendTime[Response.Settings.ActiveSendTime.end.rawValue].string
        
        // 受付可能時間帯
        let activeReceiptTime = json[Response.Settings.activeReceiptTime.rawValue]
        self.activeReceiptTime.start = activeReceiptTime[Response.Settings.ActiveReceiptTime.start.rawValue].string
        self.activeReceiptTime.end = activeReceiptTime[Response.Settings.ActiveReceiptTime.end.rawValue].string
        
        // uuid
        let beaconUuidList = json[Response.Settings.beaconUuidList.rawValue].array
        if let _beaconUuidList = beaconUuidList {
            for beaconUuid in _beaconUuidList {
                self.beaconUuidList?.append(beaconUuid.string!)
            }
        }
        
        // 受付可否API呼び出し間隔
        if let menuCheckSpan = json[Response.Settings.menuCheckSpan.rawValue].int {
            self.menuCheckSpan = menuCheckSpan
        }

    }

    // アプリサーバー間の送信間隔（秒）
    var beaconSendSpan:Int? = 60
    
    // ビーコンアプリ間の収集間隔（秒）
    var beaconCollectSpan: Int? = 1
    
    // アプリサーバー間の送信間隔（秒）
    var geoSendSpan: Int? = 600
    
    // 位置情報送信可能時間帯
    var activeSendTime: ActiveSendTime! = ActiveSendTime()
    
    //  受付可能時間帯
    var activeReceiptTime: ActiveReceiptTime! = ActiveReceiptTime()
    
    // UUID
    var beaconUuidList:[String]? = [String]()
    
    // 受付可否API呼び出し間隔
    var menuCheckSpan:Int = 60
    
    
    // 位置情報送信可能時間帯
    class ActiveSendTime {
        var start:String?
        var end:String?
        
        func isPossible () -> Bool {
            if let _ = self.start, let _ = self.end {
                let startDate = SettingsObject.createDate(self.start)
                let endDate = SettingsObject.createDate(self.end)
                let now = Date()
                return startDate <= now && now <= endDate
            } else {
                return false
            }
        }
    }
    
    //  受付可能時間帯
    class ActiveReceiptTime {
        var start:String?
        var end:String?
        
        func isPossible () -> Bool {
            if let _ = self.start, let _ = self.end {
                let startDate = SettingsObject.createDate(self.start)
                let endDate = SettingsObject.createDate(self.end)
                let now = Date()
            return startDate <= now && now <= endDate
             } else {
                return false
            }
        }
    }
    
    class func createDate(_ hm: String?) -> Date {
        let formatter = DateFormatter()
        formatter.dateFormat = DateFormatter.dateFormat(
            fromTemplate: DateFormat.standard.rawValue,
            options: 0,
            locale: Locale(identifier: "ja_JP"))
        
        let yyyMMdd = SettingsObject.stringFromDate(date: Date(), format: DateFormat.yyyMMdd)
        let string = "\(yyyMMdd) \(hm!)"
        return formatter.date(from: string)!
    }
    
    class func stringFromDate( date: Date, format: DateFormat) -> String {
        let formatter = DateFormatter()
        formatter.dateFormat = DateFormatter.dateFormat(
            fromTemplate: format.rawValue,
            options: 0,
            locale: Locale(identifier: "ja_JP"))
        
        return formatter.string(from: date)
    }
    
//    "send_span":60,
//    "collect_span":1,
//    "use_send_time":{
//    "start":"8:00",
//    "end":"18:00"
//    },
//    "receipt_time":{
//    "start":"8:00",
//    "end":"18:00"
//    }
    
}
