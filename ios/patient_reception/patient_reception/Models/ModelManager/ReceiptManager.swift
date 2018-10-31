//
//  ReceiptManager.swift
//  patient_reception
//
//  Created by nishimura on 2018/09/27.
//  Copyright © 2018年 jp.ac.tottori-u.med.hosp. All rights reserved.
//

import UIKit
import Alamofire
import Alamofire_SwiftyJSON
import SwiftyJSON


class ReceiptManager: AbstractManager<ReceiptObject> {

    
    // 端末登録
    func postRegister (register:ReceiptObject, complitionHandler:@escaping (ReceiptObject) -> Void) {
        let params: [String:AnyObject] = [
            AbstractObject.Request.unigueId.rawValue : register.uniqueId as AnyObject,
            AbstractObject.Request.deviceToken.rawValue : register.deviceToken as AnyObject,
            AbstractObject.Request.platform.rawValue : AbstractObject.Pramas.Platform.ios.rawValue as AnyObject
        ]
        let url:String = Server.getUrl(api: .receipt(action: .register))
        request(url, method: .post, params: params, complitionHandler: complitionHandler)
//        Alamofire.request(url, method: .post, parameters: params).responseSwiftyJSON { (response) in
//
//            let receiptResult = ReceiptObject()
//            receiptResult.setData(response: response)
//            complitionHandler(receiptResult)
//        }
    }
    
    // 受付
    func postEntry (register:ReceiptObject, complitionHandler:@escaping (ReceiptObject) -> Void) {
        let params: [String:AnyObject] = [
            AbstractObject.Request.unigueId.rawValue : register.uniqueId as AnyObject,
        ]
        let url:String = Server.getUrl(api: .receipt(action: .entry))
        request(url, method: .post, params: params, complitionHandler: complitionHandler)
        
//        Alamofire.request(url, method: .post, parameters: params).responseSwiftyJSON { (response) in
//
//            let receiptResult = ReceiptObject()
//            receiptResult.setData(response: response)
//            complitionHandler(receiptResult)
//        }
    }
    
    override func createObject() -> AbstractObject {
        return ReceiptObject()
    }
}
